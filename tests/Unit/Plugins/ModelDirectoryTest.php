<?php

use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;
use Actengage\Media\Plugins\ModelDirectory;
use Actengage\Media\Resources\Image;
use Tests\Unit\Plugins\PrimaryKeyExtractor;

it('stores the image in a directory resolved from the model key', function (array|string $plugin): void {
    Image::register([$plugin]);

    $model = Resource::path(__DIR__.'/../../src/image.jpeg')->save();

    expect($model->directory)->toBe('1');
})->with([
    'default key' => fn (): string => ModelDirectory::class,
    'string extractor' => fn (): array => [ModelDirectory::class, ['extractor' => PrimaryKeyExtractor::class]],
    'invokable extractor' => fn (): array => [ModelDirectory::class, ['extractor' => new class
    {
        public function __invoke(Media $model): int
        {
            return $model->id;
        }
    }]],
]);
