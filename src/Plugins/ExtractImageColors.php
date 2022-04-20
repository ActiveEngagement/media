<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Media;
use Actengage\Media\Resources\Image as Resource;
use Illuminate\Support\Collection;

class ExtractImageColors extends Plugin {

    /**
     * Boot the plugin.
     *
     * @param Collection $options
     * @return void
     */
    public static function boot(Collection $options): void
    {
        Resource::creating(function(Resource $resource, Media $model) use ($options) {
            $model->colors = $resource->palette(
                (int) $options->get('colorCount', 10),
                (int) $options->get('quality', 10)
            );
        });
    }

}