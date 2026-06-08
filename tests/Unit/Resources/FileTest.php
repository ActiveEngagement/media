<?php

use Actengage\Media\Data\Stream;
use Actengage\Media\Exceptions\InvalidResourceException;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;
use Actengage\Media\Resources\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Unit\Support\DummyFilesystem;

it('creates and stores a file resource', function (): void {
    $file = new UploadedFile(
        __DIR__.'/../../src/file.txt', 'file.txt'
    );

    $resource = Resource::make($file)
        ->disk('public')
        ->directory('files');

    expect($resource)->toBeInstanceOf(File::class);
    expect($resource->stream())->toBeInstanceOf(Stream::class);
    expect($resource->extension)->toBe('txt');
    expect($resource->mime)->toBe('text/plain');

    $model = $resource->save();

    expect($model)->toBeInstanceOf(Media::class);
    expect($model->file_exists)->toBeTrue();
    expect($model->filesize)->toBe(25);
    expect($model->extension)->toBe('txt');
    expect($model->relative_path)->toBe('files/file.txt');
    expect($model->url)->toBe('/storage/files/file.txt');

    // Ensure that by default files on disk are not deleted when the Media record is. That behavior is reserved for
    // the DeletesFromDisk plugin.
    expect($model->delete())->toBeTrue();

    Storage::disk('public')->assertExists('files/file.txt');
});

it('passes storage options through to the disk', function (): void {
    $fs = new DummyFilesystem;
    Storage::shouldReceive('disk')->andReturn($fs);

    $file = new UploadedFile(
        __DIR__.'/../../src/file.txt', 'file.txt'
    );

    Resource::make($file)
        ->disk('public')
        ->directory('files')
        ->storageOptions([
            'example' => true,
            'config' => 'one',
        ])
        ->save();

    expect($fs->options)->toBe([
        'example' => true,
        'config' => 'one',
    ]);
});

it('throws an InvalidResourceException for unreadable data', function (): void {
    File::make(12345);
})->throws(InvalidResourceException::class);
