<?php

use Actengage\Media\Data\Stream;
use Illuminate\Http\UploadedFile;

it('creates a stream from a resource', function (): void {
    $file = Stream::make(fopen(__DIR__.'/../../src/file.txt', 'r+'));

    expect($file->getSize())->toBe(25);
    expect($file->extension())->toBe('txt');
    expect($file->filename())->toBe('file.txt');
    expect($file->mime())->toBe('text/plain');
    expect($file->getContents())->toBe('This is some sample text.');
});

it('creates a stream from a path', function (): void {
    $file = Stream::make(__DIR__.'/../../src/file.txt');

    expect($file->getSize())->toBe(25);
    expect($file->extension())->toBe('txt');
    expect($file->filename())->toBe('file.txt');
    expect($file->mime())->toBe('text/plain');
    expect($file->getContents())->toBe('This is some sample text.');
});

it('creates a stream from a SplFileInfo', function (): void {
    $file = Stream::make(
        new UploadedFile(__DIR__.'/../../src/file.txt', 'file.text')
    );

    expect($file->getSize())->toBe(25);
    expect($file->extension())->toBe('txt');
    expect($file->filename())->toBe('file.txt');
    expect($file->mime())->toBe('text/plain');
    expect($file->getContents())->toBe('This is some sample text.');
});

it('creates a stream from a string', function (): void {
    $file = Stream::make('This is some sample text.', [
        'metadata' => [
            'filename' => 'file.txt',
        ],
    ]);

    expect($file->getSize())->toBe(25);
    expect($file->extension())->toBe('txt');
    expect($file->filename())->toBe('file.txt');
    expect($file->mime())->toBe('text/plain');
    expect($file->getContents())->toBe('This is some sample text.');
});

it('throws a NotReadableException for invalid data', function (): void {
    Stream::make(null);
})->throws(\Actengage\Media\Exceptions\NotReadableException::class, 'Cannot create stream using invalid data.');
