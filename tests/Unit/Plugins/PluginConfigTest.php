<?php

use Actengage\Media\Plugins\HashFilename;
use Actengage\Media\Plugins\PluginConfig;

it('builds a config from a string', function (): void {
    $config = PluginConfig::make(HashFilename::class);

    expect($config->class())->toBe(HashFilename::class);
    expect($config->options())->toHaveCount(0);
    expect($config->booted())->toBeFalse();
});

it('builds a config from an array with options', function (): void {
    $config = PluginConfig::make([HashFilename::class, ['length' => 8]]);

    expect($config->class())->toBe(HashFilename::class);
    expect($config->options()->get('length'))->toBe(8);
});

it('returns the same instance when given a config', function (): void {
    $config = PluginConfig::make(HashFilename::class);

    expect(PluginConfig::make($config))->toBe($config);
});

it('boots the underlying plugin', function (): void {
    $config = PluginConfig::make([HashFilename::class, ['length' => 8]]);

    $config->boot();

    expect($config->booted())->toBeTrue();
});

it('creates a plugin instance from the config', function (): void {
    $config = PluginConfig::make([HashFilename::class, ['length' => 8]]);

    expect($config->plugin())->toBeInstanceOf(HashFilename::class);
});

it('matches configs with the same class and options', function (): void {
    $a = PluginConfig::make([HashFilename::class, ['length' => 8]]);
    $b = PluginConfig::make([HashFilename::class, ['length' => 8]]);
    $c = PluginConfig::make([HashFilename::class, ['length' => 16]]);

    expect($a->matches($b))->toBeTrue();
    expect($a->matches($c))->toBeFalse();
});
