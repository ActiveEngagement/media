<?php

use Actengage\Media\Exceptions\InvalidResourceException;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Resources\File;
use Actengage\Media\Resources\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;

it('configures the factory', function (): void {
    Resource::configure(Config::get('media.resources'));

    expect(Resource::resource('image'))->toBe(Image::class);
    expect(Resource::resource('file'))->toBe(File::class);
    expect(Resource::resource('test'))->toBe('test');
});

it('creates a resource from a request file', function (): void {
    $file = new UploadedFile(
        __DIR__.'/../src/image.jpeg', 'image.jpeg'
    );

    request()->files->set('image', $file);

    expect(Resource::request('image'))->toBeInstanceOf(Image::class);
});

it('creates a resource from a path', function (): void {
    $resource = Resource::path(__DIR__.'/../src/image.jpeg');

    expect($resource)->toBeInstanceOf(Image::class);
    expect($resource->filename)->toBe('image.jpeg');
});

it('throws when no resource can be created from the input', function (): void {
    Resource::make(12345);
})->throws(InvalidResourceException::class, 'A resource cannot be created from the given input.');
