<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Media;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Preserves the original data as a child to the resource being created.
 * 
 * Available Options:
 * 
 * @var string filename The format of the filename of the original resource.
 *                      Defaults to '{{ $filename }}_{{ $hash }}.{{ $extension }}'.
 */
class DeletableFromDisk extends Plugin
{
    public static function boot(Collection $options): void
    {
        Media::deleting(function($media) {
            Storage::disk($media->disk)->delete($media->relative_path);
        });
    }
}