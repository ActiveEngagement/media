<?php

namespace Actengage\Media\Support;

use Illuminate\Support\Collection;

trait HasPlugins
{
    /**
     * The resource plugin instances.
     *
     * @var Collection
     */
    protected Collection $pluginInstances;

    /**
     * Resolve the plugin instance method.
     *
     * @param string $method
     * @param mixed ...$args
     * @return void
     */
    public function resolvePluginMethod(string $method, ...$args): self
    {
        $this->pluginInstances->map->$method($this, ...$args);

        return $this;
    }

    /**
     * Boot the plugins.
     *
     * @return void
     */
    public static function boot()
    {
        (new Collection(static::$plugins))
            ->each(function($plugin) {
                if(!is_array($plugin)) {
                    $plugin = [$plugin, []];
                }

                [ $class, $args ] = $plugin;

                $class::boot(new Collection(isset($args) ? $args : []));
            });
    }

    /**
     * Fluently get or set the available plugins. If no argument is passed, this
     * method acts as a getter.
     *
     * @param array $plugins
     * @return array|void
     */
    public static function plugins(array $plugins = null)
    {
        if(is_null($plugins)) {
            return static::$plugins;
        }

        static::setPlugins($plugins);
    }

    /**
     * Get the available plugins.
     *
     * @return array
     */
    public static function getPlugins(): array
    {
        return static::$plugins;
    }

    /**
     * Set the available plugins.
     *
     * @param array $plugins
     * @return void
     */
    public static function setPlugins(array $plugins)
    {
        static::$plugins = $plugins;
        static::boot();
    }

    /**
     * Add a plugin.
     *
     * @param array|mixed $plugins
     * @return void
     */
    public static function addPlugins($plugins)
    {
        static::setPlugins(array_unique(array_merge(
            static::$plugins, is_array($plugins) ? $plugins : func_get_args()
        )));
    }

    /**
     * Remove a plugin.
     *
     * @param array|mixed $plugins
     * @return void
     */
    public static function removePlugins(array $plugins = [])
    {
        static::setPlugins(array_diff(
            static::$plugins, is_array($plugins) ? $plugins : func_get_args()
        ));
    }

    /**
     * Reset plugins to original state.
     *
     * @return void
     */
    public static function flushPlugins()
    {
        static::$plugins = [];
    }
}