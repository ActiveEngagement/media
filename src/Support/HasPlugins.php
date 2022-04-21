<?php

namespace Actengage\Media\Support;

use Illuminate\Support\Collection;

trait HasPlugins
{
    /**
     * The available plugins.
     *
     * @var array
     */
    protected static array $plugins = [];

    /**
     * Boot the plugins.
     *
     * @return void
     */
    public static function boot()
    {
        (new Collection(static::$plugins))
            ->each(function($plugin) {
                if(is_array($plugin)) {
                    [$plugin, $args] = $plugin;
                }

                $plugin::boot(new Collection(isset($args) ? $args : []));
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
    public static function removePlugins($plugins)
    {
        static::setPlugins(array_diff(
            static::$plugins, is_array($plugins) ? $plugins : func_get_args()
        ));
    }
}