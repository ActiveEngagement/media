<?php

use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\ExtractImageColors;
use Actengage\Media\Plugins\HashDirectory;
use Actengage\Media\Plugins\HashFilename;
use Actengage\Media\Plugins\PreserveOriginalResource;

it('merges and removes configurations', function (): void {
    Plugin::register([
        PreserveOriginalResource::class,
        [HashDirectory::class, [
            'length' => 8,
        ]],
        'image' => [
            [ExtractImageColors::class, [
                'colorCount' => 3,
            ]],
            [HashFilename::class, [
                'length' => 8,
            ]],
        ],
        'file' => [
            [HashFilename::class, [
                'length' => 8,
            ]],
        ],
    ]);

    expect(Resource::make(__DIR__.'/../src/image.jpeg')->plugins())->toHaveCount(4);
    expect(Resource::make(__DIR__.'/../src/file.txt')->plugins())->toHaveCount(3);

    Plugin::unregister([
        [HashDirectory::class, [
            'length' => 8,
        ]],
        'file' => [
            [HashFilename::class, [
                'length' => 8,
            ]],
        ],
        'image' => [
            [HashFilename::class, [
                'length' => 8,
            ]],
        ],
    ]);

    expect(Resource::make(__DIR__.'/../src/image.jpeg')->plugins())->toHaveCount(2);
    expect(Resource::make(__DIR__.'/../src/file.txt')->plugins())->toHaveCount(1);
});

it('merges group configuration', function (): void {
    expect(Plugin::config())->toHaveCount(0);

    Plugin::registerGroup('image', [
        [HashFilename::class, [
            'length' => 8,
        ]],
    ]);

    Plugin::registerGroup('file', [
        [HashFilename::class, [
            'length' => 8,
        ]],
    ]);

    expect(Plugin::config())->toHaveCount(2);
});

it('removes group configurations', function (): void {
    Plugin::register([
        [HashDirectory::class, [
            'length' => 8,
        ]],
        'image' => [
            [HashFilename::class, [
                'length' => 8,
            ]],
        ],
        'file' => [
            [HashFilename::class, [
                'length' => 8,
            ]],
        ],
    ]);

    expect(Plugin::config())->toHaveCount(3);

    Plugin::unregisterGroup(['image', 'file']);

    expect(Plugin::config())->toHaveCount(1);
});

it('keeps plugins when unregistering a group that has no matching subjects', function (): void {
    Plugin::register([
        [HashDirectory::class, [
            'length' => 8,
        ]],
    ]);

    Plugin::unregister([
        'image' => [
            [HashFilename::class, [
                'length' => 8,
            ]],
        ],
    ]);

    expect(Plugin::config()->get('global'))->toHaveCount(1);
});
