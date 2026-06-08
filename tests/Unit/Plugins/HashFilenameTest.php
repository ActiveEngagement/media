<?php

use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\HashFilename;
use Actengage\Media\Resources\Image;

it('hashes the filename for images only', function (): void {
    Image::register([
        [HashFilename::class, [
            'length' => 8,
        ]],
    ]);

    $resource = Resource::path(__DIR__.'/../../src/image.jpeg');

    expect($resource->filename)->toHaveLength(13);

    $resource = Resource::path(__DIR__.'/../../src/file.txt');

    expect($resource->filename)->toHaveLength(8);
});
