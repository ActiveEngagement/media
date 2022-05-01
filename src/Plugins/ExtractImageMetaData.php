<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Resource;
use Actengage\Media\Media;
use Actengage\Media\Resources\Image;

/**
 * Extracts the width and height into the meta data when saving images.                    
 */
class ExtractImageMetaData extends Plugin
{
    /**
     * The resources that are ignored by the plugin.
     *
     * @var array
     */
    protected static array $compatibleResources = [
        Image::class
    ];
    
    /**
     * Runs after the `saving` event fires.
     *
     * @param Resource $resource
     * @param Media $model
     * @return void
     */
    public function saving(Resource $resource, Media $model)
    {
        $model->meta->put('width', $resource->image()->width());
        $model->meta->put('height', $resource->image()->height());
    }
}