<?php

use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\HashDirectory;
use Actengage\Media\Resources\Image;

it('hashes the directory for images only', function (): void {
    Image::register([
        [HashDirectory::class, [
            'length' => 8,
        ]],
    ]);

    $resource = Resource::path(__DIR__.'/../../src/image.jpeg');

    expect($resource->directory)->toHaveLength(8);

    $resource = Resource::path(__DIR__.'/../../src/file.txt');

    expect($resource->directory)->toBeNull();
});
