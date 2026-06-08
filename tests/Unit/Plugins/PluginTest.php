<?php

use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\Plugin;
use Actengage\Media\Resources\File;
use Actengage\Media\Resources\Image;
use Illuminate\Support\Collection;

it('exposes its options', function (): void {
    $options = new Collection;
    $options->put('length', 8);

    $plugin = new class($options) extends Plugin {};

    expect($plugin->options()->all())->toBe(['length' => 8]);
});

it('verifies a resource against compatible resources', function (): void {
    $plugin = new class(new Collection) extends Plugin
    {
        protected static array $compatibleResources = [
            Image::class,
        ];
    };

    expect($plugin->verifyResource(Resource::path(__DIR__.'/../../src/image.jpeg')))->toBeTrue();
    expect($plugin->verifyResource(Resource::path(__DIR__.'/../../src/file.txt')))->toBeFalse();
});

it('verifies a resource against ignored resources', function (): void {
    $plugin = new class(new Collection) extends Plugin
    {
        protected static array $ignoreResources = [
            File::class,
        ];
    };

    expect($plugin->verifyResource(Resource::path(__DIR__.'/../../src/image.jpeg')))->toBeTrue();
    expect($plugin->verifyResource(Resource::path(__DIR__.'/../../src/file.txt')))->toBeFalse();
});

it('returns its compatible and ignored resources', function (): void {
    expect(Plugin::compatibleResources())->toBe([]);
    expect(Plugin::ignoreResources())->toBe([]);
});
