<?php

namespace Actengage\Media\Support;

use Actengage\Media\Facades\Plugin;
use Illuminate\Support\Collection;

trait HasPlugins
{
    /**
     * The resource plugin instances.
     *
     * @var Collection
     */
    protected Collection $plugins;

    /**
     * Resolve the plugin instance method.
     *
     * @param string $method
     * @param mixed ...$args
     * @return void
     */
    public function resolvePluginMethod(string $method, ...$args): self
    {
        $this->plugins->map->$method($this, ...$args);

        return $this;
    }

    /**
     * Get the registered plugins.
     *
     * @return Collection
     */
    public function plugins(): Collection
    {
        return $this->plugins;
    }

    /**
     * Register plugins for the resource.
     *
     * @param array $plugins
     * @return Collection
     */
    public static function register(array $plugins): Collection
    {
        return Plugin::register([
            static::class => $plugins
        ]);
    }

    /**
     * Unregister plugins for the resource.
     *
     * @param array $plugins
     * @return Collection
     */
    public static function unregister(array $plugins): Collection
    {
        return Plugin::unregister([
            static::class => $plugins
        ]);
    }
}