<?php

use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\ExtractImageMetaData;
use Illuminate\Http\UploadedFile;

it('extracts the image width and height into the meta data', function (): void {
    Plugin::register([
        ExtractImageMetaData::class,
    ]);

    $file = new UploadedFile(
        __DIR__.'/../../src/image.jpeg', 'image.jpeg'
    );

    $model = Resource::make($file)
        ->resize(10, 10)
        ->save();

    expect($model->meta->get('width'))->toBe(10);
    expect($model->meta->get('height'))->toBe(10);
});
