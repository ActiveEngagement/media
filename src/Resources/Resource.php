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
use Illuminate\Events\QueuedClosure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Traits\Macroable;
use Psr\Http\Message\StreamInterface;
use ReflectionClass;
use ReflectionProperty;

/**
 * @phpstan-consistent-constructor
 *
 * @implements Arrayable<string, mixed>
 */
abstract class Resource implements Arrayable, ResourceInterface
{
    use Attributes, HasEvents, HasPlugins, Macroable {
        Attributes::__call as __callAttributes;
        Macroable::__call as __callMacros;
        Macroable::__callStatic as __callStaticMacros;
    }

    /**
     * The caption for the resource.
     */
    public ?string $caption = null;

    /**
     * The context for the resource.
     */
    public ?string $context = null;

    /**
     * The storage directory.
     */
    public ?string $directory = null;

    /**
     * The storage disk.
     */
    public ?string $disk = null;

    /**
     * The file extension.
     */
    public ?string $extension = null;

    /**
     * The file name.
     */
    public ?string $filename = null;

    /**
     * The file size.
     */
    public mixed $filesize = 0;

    /**
     * The resource meta data.
     *
     * @var Collection<array-key, mixed>|null
     */
    public ?Collection $meta = null;

    /**
     * The mime type.
     */
    public ?string $mime = null;

    /**
     * The parent model.
     */
    public ?Media $parent = null;

    /**
     * The resource tags.
     *
     * @var Collection<array-key, mixed>|null
     */
    public ?Collection $tags = null;

    /**
     * The resource title.
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
     *
     * @var Collection<array-key, mixed>|null
     */
    public ?Collection $storageOptions = null;

    /**
     * Create a new resource instance.
     */
    public function __construct(mixed $data = null)
    {
        if ($this->disk === null) {
            $default = config('filesystems.default');

            $this->disk = is_string($default) ? $default : null;
        }

        if ($data) {
            $this->initialize($data);
        }

        $this->plugins = Plugin::initialize($this);
        $this->fireEvent('initialized');
        $this->resolvePluginMethod('initialized');
    }

    /**
     * Bind events to the dispatcher if the method doesn't exist.
     *
     * @param  string  $name
     * @param  array<array-key, mixed>  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (static::hasMacro($name)) {
            return $this->__callMacros($name, $arguments);
        }

        if ($this->isObservableEvent($name)) {
            $callback = $arguments[0] ?? null;

            if ($callback instanceof Closure || $callback instanceof QueuedClosure || is_string($callback)) {
                static::registerEvent($name, $callback);
            }

            return $this;
        }

        return $this->__callAttributes($name, $arguments);
    }

    /**
     * Bind events to the dispatcher if the static method doesn't exist.
     *
     * @param  string  $name
     * @param  array<array-key, mixed>  $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if (static::hasMacro($name)) {
            return static::__callStaticMacros($name, $arguments);
        }

        $callback = $arguments[0] ?? null;

        if ($callback instanceof Closure || $callback instanceof QueuedClosure || is_string($callback)) {
            static::registerEvent($name, $callback);
        }
    }

    /**
     * Get the model attributes.
     *
     * @param  array<string, mixed>  ...$overrides
     * @return array<string, mixed>
     */
    public function attributes(array ...$overrides): array
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
        ], ...$overrides);
    }

    /**
     * Set the `caption` attribute.
     */
    public function caption(string $value): self
    {
        return $this->setAttribute('caption', $value);
    }

    /**
     * Set the `context` attribute.
     */
    public function context(string $value): self
    {
        return $this->setAttribute('context', $value);
    }

    /**
     * Set the `directory` attribute.
     */
    public function directory(string $value): self
    {
        return $this->setAttribute('directory', $value);
    }

    /**
     * Set the `disk` attribute.
     */
    public function disk(string $value): self
    {
        return $this->setAttribute('disk', $value);
    }

    /**
     * Set the `extension` attribute.
     */
    public function extension(string $value): self
    {
        $extension = $this->extension;

        $this->attribute('extension', $value);

        if ($extension !== $value) {
            $this->attribute('filename', preg_replace("/\.$extension$/", ".{$value}", (string) $this->filename));
        }

        return $this;
    }

    /**
     * Set the `filename` attribute.
     */
    public function filename(string $value): self
    {
        $extension = pathinfo($value, PATHINFO_EXTENSION);
        if ($extension !== '' && $extension !== '0') {
            $this->attribute('extension', $extension);
        } else {
            $value = sprintf('%s.%s', $value, $this->extension);
        }

        $this->attribute('filename', $value);

        return $this;
    }

    /**
     * Set the `filesize` attribute.
     *
     * @param  mixed  $value
     */
    public function filesize($value): self
    {
        if ($value instanceof StreamInterface) {
            $value = $value->getSize();
        }

        return $this->setAttribute('filesize', $value);
    }

    /**
     * Initialize the resource.
     *
     * @return void
     */
    public function initialize(mixed $data)
    {
        //
    }

    /**
     * Add an `is` callback resolver that executes when the resource matches
     * the key(s).
     */
    /**
     * @param  array<array-key, string>|string  $key
     */
    public function is(array|string $key, Closure $fn): self
    {
        if (ResourceFactory::is($this, $key)) {
            call_user_func($fn, $this);
        }

        return $this;
    }

    /**
     * Set the `meta` property.
     *
     * @param  array<array-key, mixed>|string  $key
     * @param  mixed  $value
     */
    public function meta(array|string $key, $value = null): self
    {
        if (! $this->meta instanceof Collection) {
            $this->meta = new Collection;
        }

        if (is_array($key)) {
            $this->meta = $this->meta->merge($key);
        } else {
            $this->meta->put($key, $value);
        }

        return $this;
    }

    /**
     * Set the `mime` attribute.
     */
    public function mime(string $value): self
    {
        return $this->setAttribute('mime', $value);
    }

    /**
     * Add an `not` callback resolver that executes if the condition is
     * `false`.
     */
    public function not(Closure|bool $value, Closure $fn): self
    {
        if ($value instanceof Closure) {
            $value = call_user_func($value, $this);
        }

        if ($value === false) {
            call_user_func($fn, $this);
        }

        return $this;
    }

    /**
     * Associate a parent model to the resource.
     */
    public function parent(?Media $model): self
    {
        return $this->setAttribute('parent', $model);
    }

    /**
     * Set the `title` attribute.
     */
    public function title(string $value): self
    {
        return $this->setAttribute('title', $value);
    }

    /**
     * Set the storage options passed to `Storage`.
     *
     * @param  array<array-key, mixed>  $values
     */
    public function storageOptions(array $values): self
    {
        if (! $this->storageOptions instanceof Collection) {
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
     * @return array<array-key, mixed>
     */
    public function getStorageOptionsArray(): array
    {
        return $this->storageOptions instanceof Collection ? $this->storageOptions->toArray() : [];
    }

    /**
     * Save the resource and return a model.
     */
    public function save(): Media
    {
        return DB::transaction(function () {
            $this->fireEvent('beforeSaving');
            $this->resolvePluginMethod('beforeSaving');

            $model = app()->make(Media::class, $this->attributes());

            if ($this->parent instanceof Media) {
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
     * @param  string|string[]  ...$values
     */
    public function tags(...$values): self
    {
        if (! $this->tags instanceof Collection) {
            $this->tags = new Collection;
        }

        $this->tags = $this->tags->merge(
            (new Collection($values))->flatten(1)
        );

        if ($this->tags->isNotEmpty()) {
            $this->storageOptions(['Tagging' => $this->getS3TagString($this->tags)]);
        }

        return $this;
    }

    /**
     * Add an `when` callback resolver that executes if the condition is
     * `true`.
     */
    public function when(Closure|bool $value, Closure $fn): self
    {
        if ($value instanceof Closure) {
            $value = call_user_func($value, $this);
        }

        if ($value === true) {
            call_user_func($fn, $this);
        }

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray()
    {
        $properties = (new ReflectionClass($this))
            ->getProperties(ReflectionProperty::IS_PUBLIC);

        return collect($properties)
            ->mapWithKeys(function ($property) {
                $value = $property->getValue($this);

                if ($value instanceof Arrayable) {
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
     * @param  Collection<array-key, mixed>  $tags
     */
    protected function getS3TagString(Collection $tags): string
    {
        $associativeTags = $tags->mapWithKeys(fn ($tag) => [is_scalar($tag) ? (string) $tag : '' => 'true']);

        return http_build_query($associativeTags->toArray());
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
     * @param  mixed  ...$args
     */
    public static function make(...$args): static
    {
        return new static(...$args);
    }
}
