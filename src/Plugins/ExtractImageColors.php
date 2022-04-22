<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Media;
use Actengage\Media\Resources\Image;
use ColorThief\ColorThief;
use Illuminate\Support\Collection;

class ExtractImageColors extends Plugin
{
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
    
        Image::creating(function(Image $resource, Media $model) use ($options) {
            $model->colors = $resource->palette(
                (int) $options->get('colorCount', 10),
                (int) $options->get('quality', 10)
            );
        });
    }
}