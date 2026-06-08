<?php

namespace Actengage\Media\Support;

use Actengage\Media\Contracts\Plugin as PluginContract;
use Actengage\Media\Facades\Plugin;
use Actengage\Media\Plugins\PluginConfig;
use Illuminate\Support\Collection;

trait HasPlugins
{
    /**
     * The resource plugin instances.
     *
     * @var Collection<int, PluginContract>
     */
    protected Collection $plugins;

    /**
     * Resolve the plugin instance method.
     *
     * @param  mixed  ...$args
     */
    public function resolvePluginMethod(string $method, ...$args): self
    {
        $this->plugins
            ->filter(fn (PluginContract $plugin) => $plugin->verifyResource($this))
            ->each(fn (PluginContract $plugin) => $plugin->{$method}($this, ...$args));

        return $this;
    }

    /**
     * Get the registered plugins.
     *
     * @return Collection<int, PluginContract>
     */
    public function plugins(): Collection
    {
        return $this->plugins;
    }

    /**
     * Register plugins for the resource.
     *
     * @param  array<array-key, mixed>  $plugins
     * @return Collection<string, Collection<int, PluginConfig>>
     */
    public static function register(array $plugins): Collection
    {
        return Plugin::register([
            static::class => $plugins,
        ]);
    }

    /**
     * Unregister plugins for the resource.
     *
     * @param  array<array-key, mixed>  $plugins
     * @return Collection<string, Collection<int, PluginConfig>>
     */
    public static function unregister(array $plugins): Collection
    {
        return Plugin::unregister([
            static::class => $plugins,
        ]);
    }
}
