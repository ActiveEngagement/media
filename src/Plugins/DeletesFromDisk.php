<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Media;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Deletes the underlying file on disk when a Media record is deleted.
 */
class DeletesFromDisk extends Plugin
{
    public static function boot(Collection $options): void
    {
        Media::deleting(function($media) {
            Storage::disk($media->disk)->delete($media->relative_path);
        });
    }
}