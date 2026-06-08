<?php

declare(strict_types=1);

namespace Actengage\Media\Facades;

use Actengage\Media\PluginFactory;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, \Actengage\Media\Plugins\PluginConfig>> boot()
 * @method static \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, \Actengage\Media\Plugins\PluginConfig>> config()
 * @method static \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, \Actengage\Media\Plugins\PluginConfig>> configure(array<array-key, mixed> $plugins)
 * @method static \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, \Actengage\Media\Plugins\PluginConfig>> flush()
 * @method static \Illuminate\Support\Collection<int, \Actengage\Media\Contracts\Plugin> initialize(\Actengage\Media\Contracts\Resource $resource)
 * @method static \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, \Actengage\Media\Plugins\PluginConfig>> register(array<array-key, mixed> $plugins)
 * @method static \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, \Actengage\Media\Plugins\PluginConfig>> registerGroup(string $group, array<array-key, mixed> $plugins)
 * @method static \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, \Actengage\Media\Plugins\PluginConfig>> unregister(array<array-key, mixed> $subjects = [])
 * @method static \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, \Actengage\Media\Plugins\PluginConfig>> unregisterGroup(string|string[] ...$groups)
 *
 * @see PluginFactory
 */
class Plugin extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PluginFactory::class;
    }
}
