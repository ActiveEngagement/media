<?php

use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;
use Actengage\Media\Plugins\EnforceMaximumImageDimensions;
use Illuminate\Http\UploadedFile;

it('enforces maximum image dimensions', function (): void {
    Plugin::register([
        [EnforceMaximumImageDimensions::class, [
            'width' => 100,
            'height' => 100,
        ]],
    ]);

    $file = new UploadedFile(
        __DIR__.'/../../src/image.jpeg', 'image.jpeg'
    );

    $resource = Resource::make($file);

    expect($resource->image()->width())->toBe(100);
    expect($resource->image()->height())->toBe(75);
});

it('upsizes when the upsize option is enabled', function (): void {
    Plugin::register([
        [EnforceMaximumImageDimensions::class, [
            'width' => 100,
            'height' => 100,
            'aspectRatio' => false,
            'upsize' => true,
        ]],
    ]);

    $file = new UploadedFile(
        __DIR__.'/../../src/image.jpeg', 'image.jpeg'
    );

    $resource = Resource::make($file);

    expect($resource->image()->width())->toBe(100);
    expect($resource->image()->height())->toBe(100);
});

it('ignores incompatible resources', function (): void {
    Plugin::register([
        EnforceMaximumImageDimensions::class,
    ]);

    $file = new UploadedFile(
        __DIR__.'/../../src/file.txt', 'file.txt'
    );

    expect(Resource::make($file)->save())
        ->toBeInstanceOf(Media::class);
});
