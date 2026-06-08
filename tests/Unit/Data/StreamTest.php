<?php

use Actengage\Media\Data\Stream;
use Actengage\Media\Exceptions\NotReadableException;
use Illuminate\Http\UploadedFile;

it('creates a readable stream from a variety of sources', function (Stream $file): void {
    expect($file->getSize())->toBe(25);
    expect($file->extension())->toBe('txt');
    expect($file->filename())->toBe('file.txt');
    expect($file->mime())->toBe('text/plain');
    expect($file->getContents())->toBe('This is some sample text.');
})->with([
    'resource' => fn () => Stream::make(fopen(__DIR__.'/../../src/file.txt', 'r+')),
    'path' => fn () => Stream::make(__DIR__.'/../../src/file.txt'),
    'SplFileInfo' => fn () => Stream::make(new UploadedFile(__DIR__.'/../../src/file.txt', 'file.text')),
    'string' => fn () => Stream::make('This is some sample text.', [
        'metadata' => ['filename' => 'file.txt'],
    ]),
]);

it('throws a NotReadableException for invalid data', function (): void {
    Stream::make(null);
})->throws(NotReadableException::class, 'Cannot create stream using invalid data.');
