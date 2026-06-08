<?php

use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\ExtractImageColors;
use Illuminate\Http\UploadedFile;

it('extracts image colors into the model', function (): void {
    Plugin::register([
        [ExtractImageColors::class, [
            'colorCount' => 3,
            'quality' => 10,
        ]],
    ]);

    $file = new UploadedFile(
        __DIR__.'/../../src/image.jpeg', 'image.jpeg'
    );

    $model = Resource::make($file)->save();

    expect($model->colors)->toHaveCount(3);
});
