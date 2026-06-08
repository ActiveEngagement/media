<?php

use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\HashFilename;
use Actengage\Media\Resources\Image;

it('registers and unregisters plugins for a resource', function (): void {
    Image::register([
        [HashFilename::class, ['length' => 8]],
    ]);

    expect(Resource::path(__DIR__.'/../../src/image.jpeg')->plugins())->toHaveCount(1);

    Image::unregister([
        [HashFilename::class, ['length' => 8]],
    ]);

    expect(Resource::path(__DIR__.'/../../src/image.jpeg')->plugins())->toHaveCount(0);
});
