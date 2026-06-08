<?php

use Actengage\Media\Data\Stream;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;
use Actengage\Media\Resources\Image;
use Actengage\Media\Support\ExifCoordinates;
use Actengage\Media\Support\ExifData;
use ColorThief\Color;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\StreamInterface;
use Tests\Unit\Support\DummyFilesystem;

it('creates and stores an image resource', function (): void {
    $file = new UploadedFile(
        __DIR__.'/../../src/image.jpeg', 'image.jpeg'
    );

    $resource = Image::make($file);
    $resource->disk('public')->directory('images');

    expect($resource)->toBeInstanceOf(Image::class);
    expect($resource->image())->toBeInstanceOf(Intervention\Image\Image::class);
    expect($resource->filesize)->toBe(2933093);
    expect($resource->mime)->toBe('image/jpeg');
    expect($resource->extension)->toBe('jpeg');
    expect($resource->exif)->toBeInstanceOf(ExifData::class);

    $model = $resource->save();

    expect($model)->toBeInstanceOf(Media::class);
    expect($model->file_exists)->toBeTrue();
    expect($model->filesize)->toBe(2933093);
    expect($model->extension)->toBe('jpeg');
    expect($model->relative_path)->toBe('images/image.jpeg');
    expect($model->url)->toBe('/storage/images/image.jpeg');
    expect($model->exif)->toBeInstanceOf(ExifData::class);
    expect($model->exif->coordinates())->toBeInstanceOf(ExifCoordinates::class);

    // Ensure that by default files on disk are not deleted when the Media record is. That behavior is reserved for
    // the DeletesFromDisk plugin.
    expect($model->delete())->toBeTrue();

    Storage::disk('public')->assertExists('images/image.jpeg');
});

it('passes storage options through to the disk', function (): void {
    $fs = new DummyFilesystem;
    Storage::shouldReceive('disk')->andReturn($fs);

    $file = new UploadedFile(
        __DIR__.'/../../src/image.jpeg', 'image.jpeg'
    );

    Resource::make($file)
        ->disk('public')
        ->directory('images')
        ->storageOptions([
            'example' => true,
            'config' => 'two',
        ])
        ->save();

    expect($fs->options)->toBe([
        'example' => true,
        'config' => 'two',
    ]);
});

it('extracts the extension and filename from a stream', function (): void {
    $resource = Image::make(Stream::make(__DIR__.'/../../src/image.jpeg'));

    expect($resource)->toBeInstanceOf(Image::class);
    expect($resource->extension)->toBe('jpeg');
    expect($resource->filename)->toBe('image.jpeg');
});

it('returns a stream of the image data', function (): void {
    $resource = Resource::path(__DIR__.'/../../src/image.jpeg');

    expect($resource->stream())->toBeInstanceOf(StreamInterface::class);
});

it('returns the core image resource', function (): void {
    $resource = Image::make(__DIR__.'/../../src/image.jpeg');

    expect($resource->core())->not->toBeNull();
});

it('gets the dominant color of the image', function (): void {
    $resource = Image::make(__DIR__.'/../../src/image.jpeg');

    expect($resource->color(10))->toBeInstanceOf(Color::class);
});

it('sets the exif data fluently', function (): void {
    $resource = Image::make(__DIR__.'/../../src/image.jpeg');

    $exif = new ExifData(['Make' => 'Custom']);

    expect($resource->exif($exif))->toBe($resource);
    expect($resource->exif->make)->toBe('Custom');
});
