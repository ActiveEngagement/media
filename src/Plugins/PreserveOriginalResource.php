<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Resource;
use Actengage\Media\Media;
use Actengage\Media\Resources\OriginalResource;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

/**
 * Preserves the original data as a child to the resource being created.
 *
 * Available Options:
 *
 * - `filename`: The format of the filename of the original resource. Defaults
 *   to '{{ $filename }}_{{ $hash }}.{{ $extension }}'.
 */
class PreserveOriginalResource extends Plugin
{
    /**
     * The resources that are ignored by the plugin.
     *
     * @var array<int, class-string<resource>>
     */
    protected static array $ignoreResources = [
        OriginalResource::class,
    ];

    /**
     * The original resource stream.
     */
    protected OriginalResource $resource;

    /**
     * Fires after the resource has been initialized.
     *
     * @return void
     */
    public function initialized(Resource $resource)
    {
        $this->resource = new OriginalResource($resource->stream());
    }

    /**
     * Fires after the resource has been stored.
     *
     * @return void
     */
    public function stored(Resource $resource, Media $model)
    {
        $this->resource
            ->parent($model)
            ->context($this->stringOption('context', 'original'))
            ->disk($this->stringOption('disk', (string) $model->disk))
            ->directory($this->stringOption('directory', (string) $model->directory))
            ->filename($this->generateFilename($resource))
            ->save();
    }

    /**
     * Resolve a string option with a fallback default.
     */
    protected function stringOption(string $key, string $default): string
    {
        $value = $this->options->get($key, $default);

        return is_scalar($value) ? (string) $value : $default;
    }

    /**
     * Generate a dynamic filename.
     */
    protected function generateFilename(Resource $resource): string
    {
        $filename = $this->stringOption('filename', '{{ $filename }}_{{ $hash }}.{{ $extension }}');

        $attributes = $resource->toArray();
        $resourceFilename = $attributes['filename'] ?? null;

        return Blade::render($filename, array_merge(
            $attributes,
            pathinfo(is_string($resourceFilename) ? $resourceFilename : ''),
            [
                'hash' => substr(sha1(Str::random()), 0, 8),
            ]
        ));
    }
}
