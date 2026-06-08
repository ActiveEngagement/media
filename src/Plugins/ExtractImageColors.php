<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Resource;
use Actengage\Media\Media;
use Actengage\Media\Resources\Image;
use ColorThief\ColorThief;
use ColorThief\Image\Adapter\AdapterInterface;
use Illuminate\Support\Collection;

/**
 * Extracts the colors of an image resource into the meta data.
 *
 * Available Options:
 *
 * - `colorCount`: The number of colors to extract. Defaults to 10.
 * - `quality`: The quality is the image determines how accurate the color
 *   extract will be. Higher the number, the lower the quality. This should be
 *   a tradeoff with performance and accuracy. Defaults to 10.
 */
class ExtractImageColors extends Plugin
{
    /**
     * The resources that are ignored by the plugin.
     *
     * @var array<int, class-string<resource>>
     */
    protected static array $compatibleResources = [
        Image::class,
    ];

    /**
     * Runs after the `saving` event fires.
     *
     * @return void
     */
    public function saving(Resource $resource, Media $model)
    {
        if ($resource instanceof Image) {
            $colorCount = $this->options->get('colorCount', 10);
            $quality = $this->options->get('quality', 10);

            $model->colors = $resource->palette(
                is_numeric($colorCount) ? (int) $colorCount : 10,
                is_numeric($quality) ? (int) $quality : 10
            );
        }
    }

    /**
     * Boot the plugin.
     */
    public static function boot(Collection $options): void
    {
        /**
         * Get the color palette of the image.
         *
         * @param  int  $colorCount
         * @param  int  $quality
         * @param  array|null  $area
         * @param  string  $outputFormat
         * @param  AdapterInterface|string|null  $adapter
         * @return Collection
         */
        Image::macro('palette', function (int $colorCount = 10, int $quality = 10, ?array $area = null, string $outputFormat = 'obj', AdapterInterface|string|null $adapter = null): Collection {
            $core = $this->core();

            return new Collection(ColorThief::getPalette(
                $core,
                $colorCount,
                $quality,
                $area,
                $outputFormat,
                $adapter
            ) ?? []);
        });
    }
}
