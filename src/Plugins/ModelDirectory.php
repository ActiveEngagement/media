<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Resource;
use Actengage\Media\Media;
use Actengage\Media\Resources\Resource as ConcreteResource;

/**
 * Stores the images in a directory that matches the model's primary key.
 *
 * Available Options:
 *
 * - `extractor`: An invokeable class used to extract the model attribute.
 */
class ModelDirectory extends Plugin
{
    /**
     * Fires after the resource has been initialized.
     *
     * @return void
     */
    public function saved(Resource $resource, Media $model)
    {
        $resource->directory($this->extract($model));
    }

    /**
     * Runs after the `stored` event fires.
     *
     * @return void
     */
    public function storing(Resource $resource, Media $model)
    {
        if ($resource instanceof ConcreteResource) {
            $model->directory = $resource->directory;
            $model->save();
        }
    }

    /**
     * Extract the model attribute.
     */
    protected function extract(Media $model): string
    {
        $extractor = $this->options->get('extractor');

        if (is_string($extractor)) {
            $extractor = new $extractor;
        }

        if (is_callable($extractor)) {
            return $this->stringify($extractor($model));
        }

        return $this->stringify($model->getKey());
    }

    /**
     * Coerce a scalar value into a string.
     */
    protected function stringify(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
