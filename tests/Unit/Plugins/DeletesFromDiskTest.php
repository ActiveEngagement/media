<?php

use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\DeletesFromDisk;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('deletes the file from disk when the model is deleted', function (): void {
    Plugin::register([
        DeletesFromDisk::class,
    ]);

    $file = new UploadedFile(
        __DIR__.'/../../src/image.jpeg', 'image.jpeg'
    );

    $model = Resource::make($file)
        ->directory('example')
        ->disk('public')
        ->save();

    Storage::disk('public')->assertExists('example/image.jpeg');

    expect($model->delete())->toBeTrue();

    Storage::disk('public')->assertMissing('example/image.jpeg');
});
