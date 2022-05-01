<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Resource;
use Actengage\Media\Media;
use Illuminate\Support\Facades\Storage;

/**
 * Updates the filesize on the model and resource instance after the file has
 * been saved. This plugin is useful for keeping track of the filesize of
 * resources after they have been transformed.
 */
class UpdateFilesizeAfterStoring extends Plugin
{
    /**
     * Runs after the `stored` event fires.
     *
     * @param Resource $resource
     * @param Media $resource
     * @return void
     */
    public function stored(Resource $resource, Media $model)
    {
        $resource->filesize(
            Storage::disk($model->disk)->size($model->relative_path)
        );

        $model->update([
            'filesize' => $resource->filesize
        ]);
    }
}