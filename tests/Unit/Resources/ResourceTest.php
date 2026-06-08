<?php

use Actengage\Media\Contracts\Resource as ContractsResource;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Resources\Image;
use Illuminate\Support\Facades\Event;
use Tests\Unit\Support\PlainResource;

it('runs the matching `is` callbacks', function (): void {
    $image = 0;
    $file = 0;

    Resource::path(__DIR__.'/../../src/image.jpeg')
        ->is(Image::class, function ($resource) use (&$image): void {
            expect($resource)->toBeInstanceOf(ContractsResource::class);

            $image++;
        })
        ->is('file', function () use (&$file): void {
            $file++;
        })
        ->is(['image', 'file'], function ($resource) use (&$image, &$file): void {
            $image++;
            $file++;
        });

    expect($image)->toBe(2);
    expect($file)->toBe(1);
});

it('runs the `when` callbacks for truthy conditions', function (): void {
    $truths = 0;

    Resource::path(__DIR__.'/../../src/image.jpeg')
        ->when(true, function ($resource) use (&$truths): void {
            expect($resource)->toBeInstanceOf(ContractsResource::class);

            $truths++;
        })
        ->when(function ($resource) {
            expect($resource)->toBeInstanceOf(ContractsResource::class);

            return true;
        }, function () use (&$truths): void {
            $truths++;
        })
        ->when(false, function () use (&$truths): void {
            $truths++;
        })
        ->when(fn () => false, function () use (&$truths): void {
            $truths++;
        });

    expect($truths)->toBe(2);
});

it('runs the `not` callbacks for falsy conditions', function (): void {
    $falsy = 0;

    Resource::path(__DIR__.'/../../src/image.jpeg')
        ->not(false, function ($resource) use (&$falsy): void {
            expect($resource)->toBeInstanceOf(ContractsResource::class);

            $falsy++;
        })
        ->not(function ($resource) {
            expect($resource)->toBeInstanceOf(ContractsResource::class);

            return false;
        }, function () use (&$falsy): void {
            $falsy++;
        })
        ->not(true, function () use (&$falsy): void {
            $falsy++;
        })
        ->not(fn () => true, function () use (&$falsy): void {
            $falsy++;
        });

    expect($falsy)->toBe(2);
});

it('sets and persists database attributes', function (): void {
    $resource = Resource::path(__DIR__.'/../../src/file.txt')
        ->disk($disk = 'public')
        ->directory($directory = 'a/b/c')
        ->context($context = 'testing')
        ->caption($caption = 'This is a text file used for testing.')
        ->title($title = 'This is a test!')
        ->meta([
            'a' => 1,
            'b' => 2,
        ])
        ->meta('c', 3)
        ->tags('a', ['b'])
        ->tags(['c', 'd']);

    expect($resource->disk)->toBe($disk);
    expect($resource->directory)->toBe($directory);
    expect($resource->context)->toBe($context);
    expect($resource->caption)->toBe($caption);
    expect($resource->title)->toBe($title);
    expect($resource->tags?->all())->toBe(['a', 'b', 'c', 'd']);
    expect($resource->meta?->all())->toBe(['a' => 1, 'b' => 2, 'c' => 3]);

    $model = $resource->save();

    expect($model->disk)->toBe($disk);
    expect($model->directory)->toBe($directory);
    expect($model->context)->toBe($context);
    expect($model->caption)->toBe($caption);
    expect($model->title)->toBe($title);
    expect($model->tags->all())->toBe(['a', 'b', 'c', 'd']);
    expect($model->meta->all())->toBe(['a' => 1, 'b' => 2, 'c' => 3]);
});

it('changes the filename', function (): void {
    $model = Resource::path(__DIR__.'/../../src/file.txt')
        ->filename('renamed.html')
        ->mime('plain/html')
        ->save();

    expect($model->mime)->toBe('plain/html');
    expect($model->extension)->toBe('html');
    expect($model->filename)->toBe('renamed.html');
});

it('changes the extension', function (): void {
    $model = Resource::path(__DIR__.'/../../src/file.txt')
        ->extension('html')
        ->mime('plain/html')
        ->save();

    expect($model->mime)->toBe('plain/html');
    expect($model->extension)->toBe('html');
    expect($model->filename)->toBe('file.html');
});

it('changes the filename without changing the extension', function (): void {
    $model = Resource::path(__DIR__.'/../../src/file.txt')
        ->filename('renamed')
        ->save();

    expect($model->extension)->toBe('txt');
    expect($model->filename)->toBe('renamed.txt');
});

it('changes the extension without changing the filename', function (): void {
    $model = Resource::path(__DIR__.'/../../src/file.txt')
        ->extension('html')
        ->save();

    expect($model->extension)->toBe('html');
    expect($model->filename)->toBe('file.html');
});

it('associates a parent model', function (): void {
    $parent = Resource::path(__DIR__.'/../../src/file.txt')
        ->filename('parent.txt')
        ->save();

    $child = Resource::path(__DIR__.'/../../src/file.txt')
        ->filename('child.txt')
        ->parent($parent)
        ->save();

    expect($parent->parent)->toBeNull();
    expect($child->parent?->is($parent))->toBeTrue();
    expect($parent->children)->toHaveCount(1);
});

it('formats the filesize attribute', function (): void {
    $model = Resource::path(__DIR__.'/../../src/index.html')->save();

    expect($model->size)->toBe('286 B');
});

it('initializes a resource without constructor data', function (): void {
    $resource = new Image;
    $resource->initialize(__DIR__.'/../../src/image.jpeg');

    expect($resource->save()->exists)->toBeTrue();
});

it('sets the tagging storage option from tags', function (): void {
    $resource = Resource::path(__DIR__.'/../../src/file.txt')
        ->tags(['a', 'b', 'c']);

    expect($resource->storageOptions?->get('Tagging'))->toBe('a=true&b=true&c=true');
});

it('registers an observable event via an instance call', function (): void {
    Event::fake();

    $resource = Image::make(__DIR__.'/../../src/image.jpeg');

    expect($resource->saving(function (): void {
        //
    }))->toBe($resource);
});

it('registers an observable event via a static call', function (): void {
    expect(Image::saved(function (): void {
        //
    }))->toBeNull();
});

it('calls a registered static macro', function (): void {
    Image::macro('staticGreeting', fn (): string => 'hello');

    expect(Image::staticGreeting())->toBe('hello');
});

it('runs the abstract resource initialize for resources without an override', function (): void {
    $resource = new PlainResource('some data');

    expect($resource)->toBeInstanceOf(PlainResource::class);
    expect($resource->disk)->not->toBeNull();
});
