<?php

use Actengage\Media\Media;
use Illuminate\Database\Eloquent\Builder;

beforeEach(function (): void {
    foreach (['a', 'b', 'c'] as $context) {
        Media::factory()->createOne([
            'disk' => 'local',
            'context' => $context,
            'filename' => "$context-1.txt",
            'caption' => "$context-1",
            'title' => "$context-1",
            'mime' => 'text/plain',
            'extension' => 'txt',
            'filesize' => 25,
        ]);

        Media::factory()->createOne([
            'disk' => 'public',
            'context' => $context,
            'filename' => "$context-2.html",
            'caption' => "$context-2",
            'title' => "$context-2",
            'mime' => 'text/html',
            'extension' => 'html',
            'filesize' => 286,
        ]);
    }
});

it('scopes by caption', /** @param array<int, array<string>|string> $args */ function (array $args, int $expected): void {
    expect(Media::caption(...$args)->count())->toBe($expected);
})->with([
    'single' => [['a-1'], 1],
    'multiple' => [['a-1', 'a-2'], 2],
    'array' => [[['a-1', 'a-2']], 2],
]);

it('scopes by context', function (array $args, int $expected): void {
    expect(Media::context(...$args)->count())->toBe($expected);
})->with([
    'single' => [['a'], 2],
    'multiple' => [['a', 'b'], 4],
    'array' => [[['a', 'b']], 4],
]);

it('scopes by disk', function (array $args, int $expected): void {
    expect(Media::disk(...$args)->count())->toBe($expected);
})->with([
    'single' => [['local'], 3],
    'multiple' => [['local', 'public'], 6],
    'array' => [[['local', 'public']], 6],
]);

it('scopes by extension', function (array $args, int $expected): void {
    expect(Media::extension(...$args)->count())->toBe($expected);
})->with([
    'single' => [['txt'], 3],
    'multiple' => [['txt', 'html'], 6],
    'array' => [[['txt', 'html']], 6],
]);

it('scopes by filename', function (array $args, int $expected): void {
    expect(Media::filename(...$args)->count())->toBe($expected);
})->with([
    'single' => [['a-1.txt'], 1],
    'multiple' => [['a-1.txt', 'a-2.html'], 2],
    'array' => [[['a-1.txt', 'a-2.html']], 2],
]);

it('scopes by filesize', function (array $args, int $expected): void {
    expect(Media::filesize(...$args)->count())->toBe($expected);
})->with([
    'single' => [[25], 3],
    'array' => [[[25, 286]], 6],
]);

it('scopes by mime', function (array $args, int $expected): void {
    expect(Media::mime(...$args)->count())->toBe($expected);
})->with([
    'single' => [['text/plain'], 3],
    'array' => [[['text/plain', 'text/html']], 6],
]);

it('scopes by title', function (array $args, int $expected): void {
    expect(Media::title(...$args)->count())->toBe($expected);
})->with([
    'single' => [['a-1'], 1],
    'multiple' => [['a-1', 'a-2'], 2],
    'array' => [[['a-1', 'a-2']], 2],
]);

it('scopes by meta', function (): void {
    expect(Media::meta(['width' => 10]))->toBeInstanceOf(Builder::class);
});

it('scopes by tags', function (array $args, int $expected): void {
    Media::factory()->createOne(['filename' => 'tagged-1.txt', 'tags' => ['red', 'green']]);
    Media::factory()->createOne(['filename' => 'tagged-2.txt', 'tags' => ['blue']]);

    expect(Media::tag(...$args)->count())->toBe($expected);
})->with([
    'single tag' => [['red'], 1],
    'multiple tags' => [['red', 'blue'], 2],
    'array of tags' => [[['green']], 1],
]);

it('scopes without tags', function (): void {
    Media::factory()->createOne(['filename' => 'tagged-1.txt', 'tags' => ['red', 'green']]);
    Media::factory()->createOne(['filename' => 'tagged-2.txt', 'tags' => ['blue']]);

    expect(Media::withoutTag('red')->pluck('filename'))->not->toContain('tagged-1.txt');
    expect(Media::withoutTags(['red', 'green'])->pluck('filename'))->not->toContain('tagged-1.txt');
});
