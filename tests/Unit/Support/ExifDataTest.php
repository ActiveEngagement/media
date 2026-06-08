<?php

use Actengage\Media\Support\ExifCoordinates;
use Actengage\Media\Support\ExifData;
use Intervention\Image\ImageManagerStatic;

it('reads exif data from an image', function (): void {
    $img = ImageManagerStatic::make(
        __DIR__.'/../../src/image.jpeg'
    );

    $exif = new ExifData($img);

    expect($exif->model)->toBe('iPhone 7 Plus');
    expect($exif->make)->toBe('Apple');
    expect($exif->coordinates())->toBeInstanceOf(ExifCoordinates::class);
    expect($exif->latitude())->toBe(38.98211388888889);
    expect($exif->longitude())->toBe(-104.9599);
    expect($exif->thisKeyDoesntExist)->toBeNull();
});

it('returns null coordinates when no GPS data is present', function (): void {
    $exif = new ExifData([]);

    expect($exif->latitude())->toBeNull();
    expect($exif->longitude())->toBeNull();
});

it('handles a zero denominator in coordinate fractions', function (): void {
    $exif = new ExifData([
        'GPSLatitude' => ['38/0', '58/1', '55/1'],
        'GPSLatitudeRef' => 'N',
        'GPSLongitude' => ['104/0', '57/1', '35/1'],
        'GPSLongitudeRef' => 'W',
    ]);

    expect($exif->latitude())->toBeFloat();
    expect($exif->longitude())->toBeFloat();
});

it('serializes exif data to an array and json', function (): void {
    $exif = new ExifData(['Make' => 'Apple', 'Model' => 'iPhone']);

    expect($exif->toArray())->toBe(['Make' => 'Apple', 'Model' => 'iPhone']);
    expect($exif->toJson())->toBe('{"Make":"Apple","Model":"iPhone"}');
});
