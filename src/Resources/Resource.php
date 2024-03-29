<?php

namespace Actengage\Media\Resources;

use Actengage\Media\Contracts\Resource as ResourceInterface;
use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource as ResourceFactory;
use Actengage\Media\Media;
use Actengage\Media\Support\Attributes;
use Actengage\Media\Support\HasEvents;
use Actengage\Media\Support\HasPlugins;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Traits\Macroable;
use Psr\Http\Message\StreamInterface;
use ReflectionClass;
use ReflectionProperty;

abstract class Resource implements ResourceInterface, Arrayable
{
    use Attributes, HasEvents, HasPlugins, Macroable {
		Attributes::__call as __callAttributes;
        Macroable::__call as __callMacros;
        Macroable::__callStatic as __callStaticMacros;
	}

    /**
     * The caption for the resource.
     *
     * @var string|null
     */
    public ?string $caption = null;

    /**
     * The context for the resource.
     *
     * @var string|null
     */
    public ?string $context = null;

    /**
     * The storage directory.
     *
     * @var string|null
     */
    public ?string $directory = null;

    /**
     * The storage disk.
     *
     * @var string|null
     */
    public ?string $disk = null;

    /**
     * The file extension.
     *
     * @var string|null
     */
    public ?string $extension = null;

    /**
     * The file name.
     *
     * @var string|null
     */
    public ?string $filename = null;

    /**
     * The file size.
     *
     * @var mixed
     */
    public mixed $filesize = 0;

    /**
     * The resource meta data.
     *
     * @var ?Collection
     */
    public ?Collection $meta = null;

    /**
     * The mime type.
     *
     * @var string|null
     */
    public ?string $mime = null;

    /**
     * The parent model.
     *
     * @var Media|null
     */
    public ?Media $parent = null;

    /**
     * The resource tags.
     *
     * @var ?Collection
     */
    public ?Collection $tags = null;

    /**
     * The resource title.
     *
     * @var string|null
     */
    public ?string $title = null;

    /**
     * The storage options.
     * 
     * An array of options to be passed to Laravel's `Storage` facade.
     * 
     * For example, when using Amazon S3, a `Tagging` option may be used to set S3 tags:
     * 
     * ```php
     * Resouce::make(...)
     *   ->storageOptions([
     *     'Tagging' => 'category=image'
     *   ])
     *   ->save();
     * ```
     * 
     * Note that these options will not be persisted to the database.
     */
    public ?Collection $storageOptions = null;

    /**
     * Create a new resource instance.
     *
     * @param mixed $data
     * @return void
     */
    public function __construct(mixed $data = null)
    {        
        $this->disk = $this->disk ?? config('filesystems.default');

        if($data) {
            $this->initialize($data);
        }

        $this->plugins = Plugin::initialize($this);
        $this->fireEvent('initialized');
        $this->resolvePluginMethod('initialized');
    }

    /**
     * Bind events to the dispatcher if the method doesn't exist.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if(static::hasMacro($name)) {
            return $this->__callMacros($name, $arguments);
        }

        if($this->isObservableEvent($name)) {
            static::registerEvent($name, ...$arguments);
            
            return $this;
        }

        return $this->__callAttributes($name, $arguments);
    }

    /**
     * Bind events to the dispatcher if the static method doesn't exist.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if(static::hasMacro($name)) {
            return static::__callStaticMacros($name, $arguments);
        }
        
        static::registerEvent($name, ...$arguments);
    }

    /**
     * Get the model attributes.
     *
     * @return array
     */
    public function attributes(): array
    {
        return array_merge([
            'caption' => $this->caption,
            'context' => $this->context,
            'disk' => $this->disk,
            'directory' => $this->directory,
            'extension' => $this->extension,
            'filename' => $this->filename,
            'filesize' => $this->filesize,
            'meta' => $this->meta,
            'mime' => $this->mime,
            'tags' => $this->tags,
            'title' => $this->title,
        ], ...func_get_args());
    }

    /**
     * Set the `caption` attribute.
     *
     * @param string $value
     * @return self
     */
    public function caption(string $value): self
    {
        return $this->attribute('caption', $value);
    }

    /**
     * Set the `context` attribute.
     *
     * @param string $value
     * @return self
     */
    public function context(string $value): self
    {
        return $this->attribute('context', $value);
    }

    /**
     * Set the `directory` attribute.
     *
     * @param string $value
     * @return self
     */
    public function directory(string $value): self
    {
        return $this->attribute('directory', $value);
    }

    /**
     * Set the `disk` attribute.
     *
     * @param string $value
     * @return self
     */
    public function disk(string $value): self
    {
        return $this->attribute('disk', $value);
    }

    /**
     * Set the `extension` attribute.
     *
     * @param string $value
     * @return self
     */
    public function extension(string $value): self
    {
        $extension = $this->extension;
        
        $this->attribute('extension', $value);

        if($extension !== $value) {
            $this->attribute('filename', preg_replace("/\.$extension$/", ".{$value}", $this->filename));
        }

        return $this;
    }

    /**
     * Set the `filename` attribute.
     *
     * @param string $value
     * @return self
     */
    public function filename(string $value): self
    {
        if($extension = pathinfo($value, PATHINFO_EXTENSION)) {
            $this->attribute('extension', $extension);
        }
        else {
            $value = sprintf('%s.%s', $value, $this->extension);
        }

        $this->attribute('filename', $value);

        return $this;
    }

    /**
     * Set the `filesize` attribute.
     *
     * @param mixed $value
     * @return self
     */
    public function filesize($value): self
    {
        if($value instanceof StreamInterface) {
            $value = $value->getSize();
        }

        return $this->attribute('filesize', $value);
    }

    /**
     * Initialize the resource.
     *
     * @param mixed $data
     * @return void
     */
    public function initialize(mixed $data)
    {
        //
    }

    /**
     * Add an `is` callback resolver that executes when the resource matches
     * the key(s).
     *
     * @param array|string $key
     * @param Closure $fn
     * @return self
     */
    public function is(array|string $key, Closure $fn): self
    {
        if(ResourceFactory::is($this, $key)) {
            call_user_func($fn, $this);
        }

        return $this;
    }

    /**
     * Set the `meta` property.
     *
     * @param array|string $key
     * @param mixed $value
     * @return self
     */
    public function meta(array|string $key, $value = null): self
    {
        if(!isset($this->meta)) {
            $this->meta = new Collection();
        }

        if(is_array($key)) {
            $this->meta = $this->meta->merge($key);
        }
        else {
            $this->meta->put($key, $value);
        }

        return $this;
    }

    /**
     * Set the `mime` attribute.
     *
     * @param string $value
     * @return self
     */
    public function mime(string $value): self
    {
        return $this->attribute('mime', $value);
    }

    /**
     * Add an `not` callback resolver that executes if the condition is
     * `false`.
     *
     * @param Closure|boolean $value
     * @param Closure $fn
     * @return self
     */
    public function not(Closure|bool $value, Closure $fn): self
    {
        if($value instanceof Closure) {
            $value = call_user_func($value, $this);
        }
        
        if($value === false) {
            call_user_func($fn, $this);
        }

        return $this;
    }

    /**
     * Associate a parent model to the resource.
     *
     * @param Media|null $model
     * @return self
     */
    public function parent(?Media $model): self
    {
        return $this->attribute('parent', $model);
    }

    /**
     * Set the `title` attribute.
     *
     * @param string $value
     * @return self
     */
    public function title(string $value): self
    {
        return $this->attribute('title', $value);
    }

    /**
     * Set the storage options passed to `Storage`.
     */
    public function storageOptions(array $values): self
    {
        if(!$this->storageOptions) {
            $this->storageOptions = collect();
        }

        $this->storageOptions = $this->storageOptions->merge($values);

        return $this;
    }

    /**
     * Gets an array of storage options.
     * 
     * Converts `$this->storageOptions` to an array if it exists, or returns an empty array otherwise.
     * 
     * @return array
     */
    public function getStorageOptionsArray(): array
    {
        return $this->storageOptions ? $this->storageOptions->toArray() : [];
    }

    /**
     * Save the resource and return a model.
     *
     * @return Media|boolean
     */
    public function save(): Media|bool
    {
        return DB::transaction(function() {
            $this->fireEvent('beforeSaving');
            $this->resolvePluginMethod('beforeSaving');

            $model = app()->make(Media::class, $this->attributes());

            if($this->parent) {
                $model->parent()->associate($this->parent);
            }

            $this->fireEvent('saving', $model);
            $this->resolvePluginMethod('saving', $model);

            $model->save();

            $this->fireEvent('saved', $model);
            $this->resolvePluginMethod('saved', $model);

            $this->fireEvent('storing', $model);
            $this->resolvePluginMethod('storing', $model);

            $this->store($model);

            $this->fireEvent('stored', $model);
            $this->resolvePluginMethod('stored', $model);

            return $model;
        });
    }

    /**
     * Set the `tags` property.
     *
     * @param string[] ...$values
     * @return self
     */
    public function tags(...$values): self
    {
        if(!isset($this->tags)) {
            $this->tags = new Collection();
        }

        $this->tags = $this->tags->merge(
            (new Collection($values))->flatten(1)
        );

        if ($this->tags->isNotEmpty()) {
            $this->storageOptions([ 'Tagging' => $this->getS3TagString($this->tags) ]);
        }

        return $this;
    }

    /**
     * Add an `when` callback resolver that executes if the condition is
     * `true`.
     *
     * @param Closure|boolean $value
     * @param Closure $fn
     * @return self
     */
    public function when(Closure|bool $value, Closure $fn): self
    {
        if($value instanceof Closure) {
            $value = call_user_func($value, $this);
        }
        
        if($value === true) {
            call_user_func($fn, $this);
        }

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray()
    {
        $properties = (new ReflectionClass($this))
            ->getProperties(ReflectionProperty::IS_PUBLIC);

        return collect($properties)
            ->mapWithKeys(function($property) {
                $value = $property->getValue($this);

                if($value instanceof Arrayable) {
                    $value = $value->toArray();
                }

                return [$property->getName() => $value];
            })
            ->all();
    }

    /**
     * Converts a list of tags to an S3 tag string.
     * 
     * Converts the given `Collection` of single "tags" into an S3-compatible key-value query string where each tag is
     * set to `'true'`.
     * 
     * For example:
     * 
     * ```php
     * $tags = collect(['one', 'two', 'three']);
     * $this->getS3TagString($tags); // => "one=true&two=true&three=true"
     * ```
     * 
     * @param Collection $tags
     * @return string
     */
    protected function getS3TagString(Collection $tags): string
    {
        $associativeTags = $tags->mapWithKeys(fn ($tag) => [$tag => 'true']);
        $queryString = http_build_query($associativeTags->toArray());

        return $queryString;
    }


    /**
     * Boot the resource.
     *
     * @return void
     */
    public static function boot()
    {
        //
    }

    /**
     * Create a new instance of the resource.
     *
     * @param mixed ...$args
     * @return self
     */
    public static function make(...$args)
    {
        return new static(...$args);
    }
}