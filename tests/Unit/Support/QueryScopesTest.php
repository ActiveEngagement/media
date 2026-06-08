<?php

use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;

beforeEach(function (): void {
    foreach (['a', 'b', 'c'] as $context) {
        Resource::make(__DIR__.'/../../src/file.txt')
            ->disk('local')
            ->filename("$context-1")
            ->context($context)
            ->caption("$context-1")
            ->title("$context-1")
            ->save();

        Resource::make(__DIR__.'/../../src/index.html')
            ->disk('public')
            ->filename("$context-2")
            ->context($context)
            ->caption("$context-2")
            ->title("$context-2")
            ->save();
    }
});

it('scopes by caption', function (): void {
    expect(Media::caption('a-1')->count())->toBe(1);
    expect(Media::caption('a-1', 'a-2')->count())->toBe(2);
    expect(Media::caption(['a-1', 'a-2'])->count())->toBe(2);
});

it('scopes by context', function (): void {
    expect(Media::context('a')->count())->toBe(2);
    expect(Media::context('a', 'b')->count())->toBe(4);
    expect(Media::context(['a', 'b'])->count())->toBe(4);
});

it('scopes by disk', function (): void {
    expect(Media::disk('local')->count())->toBe(3);
    expect(Media::disk('local', 'public')->count())->toBe(6);
    expect(Media::disk(['local', 'public'])->count())->toBe(6);
});

it('scopes by extension', function (): void {
    expect(Media::extension('txt')->count())->toBe(3);
    expect(Media::extension('txt', 'html')->count())->toBe(6);
    expect(Media::extension(['txt', 'html'])->count())->toBe(6);
});

it('scopes by filename', function (): void {
    expect(Media::filename('a-1.txt')->count())->toBe(1);
    expect(Media::filename('a-1.txt', 'a-2.html')->count())->toBe(2);
    expect(Media::filename(['a-1.txt', 'a-2.html'])->count())->toBe(2);
});

it('scopes by filesize', function (): void {
    expect(Media::filesize(25)->count())->toBe(3);
    expect(Media::filesize([25, 286])->count())->toBe(6);
});

it('scopes by mime', function (): void {
    expect(Media::mime('text/plain')->count())->toBe(3);
    expect(Media::mime(['text/plain', 'text/html'])->count())->toBe(6);
});

it('scopes by title', function (): void {
    expect(Media::title('a-1')->count())->toBe(1);
    expect(Media::title('a-1', 'a-2')->count())->toBe(2);
    expect(Media::title(['a-1', 'a-2'])->count())->toBe(2);
});

it('scopes by meta', function (): void {
    expect(Media::meta(['width' => 10]))
        ->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
});

it('scopes by tags', function (): void {
    Resource::make(__DIR__.'/../../src/file.txt')
        ->filename('tagged-1')
        ->tags(['red', 'green'])
        ->save();

    Resource::make(__DIR__.'/../../src/file.txt')
        ->filename('tagged-2')
        ->tags(['blue'])
        ->save();

    expect(Media::tag('red')->count())->toBe(1);
    expect(Media::tags('red', 'blue')->count())->toBe(2);
    expect(Media::tags(['green'])->count())->toBe(1);
});

it('scopes without tags', function (): void {
    Resource::make(__DIR__.'/../../src/file.txt')
        ->filename('tagged-1')
        ->tags(['red', 'green'])
        ->save();

    Resource::make(__DIR__.'/../../src/file.txt')
        ->filename('tagged-2')
        ->tags(['blue'])
        ->save();

    expect(Media::withoutTag('red')->pluck('filename'))->not->toContain('tagged-1');
    expect(Media::withoutTags(['red', 'green'])->pluck('filename'))->not->toContain('tagged-1');
});
