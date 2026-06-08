<?php

use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\PreserveOriginalResource;

it('preserves the original image as a child resource', function (): void {
    Plugin::register([
        PreserveOriginalResource::class,
    ]);

    $model = Resource::path(__DIR__.'/../../src/image.jpeg')
        ->disk('local')
        ->greyscale()
        ->save();

    expect($model->children()->context('original')->count())->toBe(1);
    expect($model->children()->context('original')->first()->disk)->toBe('local');
    expect(preg_match('/\w+_\w{8}.jpeg/', $model->children()->context('original')->first()->filename))->toBe(1);
});

it('preserves the original file as a child resource', function (): void {
    Plugin::register([
        PreserveOriginalResource::class,
    ]);

    $model = Resource::path(__DIR__.'/../../src/file.txt')
        ->disk('local')
        ->save();

    expect($model->children()->context('original')->count())->toBe(1);
    expect($model->children()->context('original')->first()->disk)->toBe('local');
    expect(preg_match('/\w+_\w{8}.txt/', $model->children()->context('original')->first()->filename))->toBe(1);
});
