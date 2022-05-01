<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Resource;
use Actengage\Media\Media;
use Actengage\Media\Resources\Image;
use ColorThief\ColorThief;
use Illuminate\Support\Collection;

/**
 * Extracts the colors of an image resource into the meta data.
 * 
 * Available Options:
 * 
 * @var mixed $colorCount The number of colors to extract. Defaults to 10.
 * @var mixed $quality The quality is the image determines how accurate the
 *                     color extract will be. Higher the number, the lower the
 *                     quality. This should be a tradeoff with performance and
 *                     accuracy. Defaults to 10.                      
 */
class ExtractImageColors extends Plugin
{
    /**
     * Runs after the `saving` event fires.
     *
     * @param Resource $resource
     * @param Media $model
     * @return void
     */
    public function saving(Resource $resource, Media $model)
    {
        $model->colors = $resource->palette(
            (int) $this->options->get('colorCount', 10),
            (int) $this->options->get('quality', 10)
        );
    }

    /**
     * Boot the plugin.
     *
     * @param Collection $options
     * @return void
     */
    public static function boot(Collection $options): void
    {
        /**
         * Get the color palette of the image.
         *
         * @param integer $colorCount
         * @param integer $quality
         * @param array|null $area
         * @param string $outputFormat
         * @param \ColorThief\Image\Adapter\AdapterInterface|string|null $adapter 
         * @return \Illuminate\Support\Collection
         */
        Image::macro('palette', function(
            int $colorCount = 10,
            int $quality = 10,
            ?array $area = null,
            string $outputFormat = 'obj',
            $adapter = null
        ): Collection {
            return new Collection(ColorThief::getPalette(
                $this->image->getCore(),
                $colorCount,
                $quality,
                $area,
                $outputFormat,
                $adapter
            ));
        });
    }
}