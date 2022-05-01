<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Resource;
use Actengage\Media\Media;

/**
 * Stores the images in a directory that matches the model's primary key.
 * 
 * Available Options:
 * 
 * @var string extractor An invokeable class used to extract the model attribute.
 */
class ModelDirectory extends Plugin
{
    /**
     * Fires after the resource has been initialized.
     *
     * @param Resource $resource
     * @return void
     */
    public function saved(Resource $resource, Media $model)
    {
        $resource->directory($this->extract($model));
    }

    /**
     * Runs after the `stored` event fires.
     *
     * @param Resource $resource
     * @param Media $model
     * @return void
     */
    public function storing(Resource $resource, Media $model)
    {
        $model->directory = $resource->directory;
        $model->save();
    }

    /**
     * Extract the model attribute.
     *
     * @param Media $model
     * @return mixed
     */
    protected function extract(Media $model)
    {
        if(!$extractor = $this->options->get('extractor')) {
            return $model->getKey();
        }
        
        if(is_string($extractor)) {
            $extractor = new $extractor;
        }

        return $extractor($model);
    }
}