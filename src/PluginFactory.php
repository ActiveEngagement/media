<?php

namespace Actengage\Media;

use Actengage\Media\Contracts\Resource;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class PluginFactory
{
    /**
     * An array of plugins
     *
     * @var Collection
     */
    protected Collection $plugins;

    /**
     * Create an instance of the plugin factory.
     *
     * @param array $config
     */
    public function __construct(Application $app)
    {
        $this->plugins = (new Collection(
            Arr::get($app->config['media'], 'plugins', [])
        ))->groupBy(function($subject, $key) use ($app) {
            return is_numeric($key) ? 'global' : $app[ResourceFactory::class]->resource($key);
        })->map(function($group, $key) {
            return $key === 'global' ? $group : $group->flatten(1);
        })->map(function($group) {
            return $group->map(function($plugin) {
                return $this->format($plugin);
            });
        });
    }

    /**
     * Run the `boot()` methods on the applicable plugins.
     *
     * @return void
     */
    public function boot()
    {
        $this->plugins->flatten(1)->each(function($plugin) {
            [ $class, $options ] = $plugin;

            $class::boot($options);
        });
    }

    /**
     * Format the plugin class or array so it is ready to be used.
     *
     * @param array|string $plugin
     * @return array
     */
    public function format(array|string $plugin): array
    {
        [ $class, $options ] = is_array($plugin)
            ? $plugin
            : [ $plugin, [] ];

        return [ $class, new Collection($options) ];
    }

    /**
     * Initialize the plugins.
     *
     * @param Resource $resource
     * @param mixed ...$args
     * @return Collection
     */
    public function initialize(Resource $resource): Collection
    {
        $class = get_class($resource);

        return $this->plugins->only('global', $class)
            ->flatten(1)
            ->merge((new Collection($class::plugins()))->map(function($plugin) {
                return $this->format($plugin);
            }))
            ->map(function($plugin) {
                [ $class, $options ] = $plugin;

                return new $class($options);
            });
    }
}
