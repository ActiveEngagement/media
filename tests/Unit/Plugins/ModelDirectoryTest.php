<?php

use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;
use Actengage\Media\Plugins\ModelDirectory;
use Actengage\Media\Resources\Image;
use Tests\Unit\Plugins\PrimaryKeyExtractor;

it('stores the image in a directory matching the model key', function (): void {
    Image::register([
        ModelDirectory::class,
    ]);

    $model = Resource::path(__DIR__.'/../../src/image.jpeg')->save();

    expect($model->directory)->toBe('1');
});

it('uses a string extractor class to resolve the directory', function (): void {
    Image::register([
        [ModelDirectory::class, [
            'extractor' => PrimaryKeyExtractor::class,
        ]],
    ]);

    $model = Resource::path(__DIR__.'/../../src/image.jpeg')->save();

    expect($model->directory)->toBe('1');
});

it('uses an invokable extractor instance to resolve the directory', function (): void {
    Image::register([
        [ModelDirectory::class, [
            'extractor' => new class
            {
                public function __invoke(Media $model)
                {
                    return $model->getKey();
                }
            },
        ]],
    ]);

    $model = Resource::path(__DIR__.'/../../src/image.jpeg')->save();

    expect($model->directory)->toBe('1');
});
