<?php

use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;
use ColorThief\Color;

it('formats a zero-byte filesize', function (): void {
    $media = new Media;

    expect($media->size)->toBe('0 B');
});

it('formats a kilobyte filesize', function (): void {
    $media = new Media(['filesize' => 2048]);

    expect($media->size)->toBe('2 KB');
});

it('reports whether the underlying file exists', function (): void {
    $model = Resource::path(__DIR__.'/../src/file.txt')
        ->disk('public')
        ->save();

    expect($model->file_exists)->toBeTrue();

    $model->delete();
    $model->filename = 'missing.txt';

    expect($model->file_exists)->toBeFalse();
});

it('casts colors to and from the database', function (): void {
    $model = Resource::path(__DIR__.'/../src/image.jpeg')->save();

    $model->colors = [new Color(255, 0, 0)];
    $model->save();

    expect($model->fresh()->colors->first())->toBeInstanceOf(Color::class);
    expect($model->fresh()->colors->first()->getHex('#'))->toBe('#ff0000');
});
