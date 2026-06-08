<?php

use Actengage\Media\Facades\Resource;
use Actengage\Media\Support\ExifData;

it('casts exif data when persisting to the database', function (): void {
    $model = Resource::path(__DIR__.'/../../src/image.jpeg')->save();

    $model->exif = new ExifData(['Make' => 'TestMake']);
    $model->save();

    expect($model->refresh()->exif)->toBeInstanceOf(ExifData::class);
    expect($model->refresh()->exif->make)->toBe('TestMake');
});
