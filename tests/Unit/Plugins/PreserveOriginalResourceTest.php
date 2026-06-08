<?php

use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\PreserveOriginalResource;
use Actengage\Media\Resources\Image;

it('preserves the original image as a child resource', function (): void {
    Plugin::register([
        PreserveOriginalResource::class,
    ]);

    $model = Image::make(__DIR__.'/../../src/image.jpeg')
        ->greyscale()
        ->disk('local')
        ->save();

    expect($model->children()->context('original')->count())->toBe(1);
    expect($model->children()->context('original')->first()?->disk)->toBe('local');
    expect($model->children()->context('original')->first()?->filename)->toMatch('/\w+_\w{8}.jpeg/');
});

it('preserves the original file as a child resource', function (): void {
    Plugin::register([
        PreserveOriginalResource::class,
    ]);

    $model = Resource::path(__DIR__.'/../../src/file.txt')
        ->disk('local')
        ->save();

    expect($model->children()->context('original')->count())->toBe(1);
    expect($model->children()->context('original')->first()?->disk)->toBe('local');
    expect($model->children()->context('original')->first()?->filename)->toMatch('/\w+_\w{8}.txt/');
});
