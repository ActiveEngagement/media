<?php

namespace Actengage\Media;

use Actengage\Media\Contracts\Resource;
use Actengage\Media\Facades\Resource as ResourceFactory;
use Actengage\Media\Plugins\PluginConfig;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;

class PluginFactory
{
    /**
     * An array of plugins
     *
     * @var Collection
     */
    protected Collection $config;

    /**
     * Create an instance of the plugin factory.
     *
     * @param array $config
     */
    public function __construct(Application $app)
    {
        $this->config = $this->configure($app->config['media.plugins']);
    }

    /**
     * Run the `boot()` methods on the applicable plugins.
     *
     * @return Collection
     */
    public function boot(): Collection
    {
        return $this->config->each(function($plugins) {
            return $plugins->where(function($plugin) {
                return !$plugin->booted();
            })->each(function($plugin) {
                return $plugin->boot();
            });
        });
    }

    /**
     * Get the configuration.
     *
     * @return Collection
     */
    public function config(): Collection
    {
        return $this->config;
    }

    /**
     * Configure the plugins.
     *
     * @param array $plugins
     * @return Collection
     */
    public function configure(array $plugins): Collection
    {
        return (new Collection($plugins))
            ->groupBy(function($subject, $key) {
                return is_numeric($key) ? 'global' : ResourceFactory::resource($key);
            })->map(function($group, $key) {
                return $key == 'global' ? $group : $group->flatten(1);
            })->map(function($group) {
                return $group->map(function($plugin) {
                    return PluginConfig::make($plugin);
                });
            });
    }

    /**
     * Flush all the plugins from the config.
     *
     * @return Collection
     */
    public function flush(): Collection
    {
        return $this->config = new Collection();
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
        return $this->config->only('global', get_class($resource))
            ->flatten(1)
            ->map(function($config) use ($resource) {
                return $config->plugin($resource);
            });
    }

    /**
     * Register plugins into the existing configuration.
     *
     * @param array $plugins
     * @return Collection
     */
    public function register(array $plugins): Collection
    {
        $this->configure($plugins)
            ->reduce(function($carry, $plugins, $group) {
                if(!$carry->has($group)) {
                    $carry->put($group, new Collection());
                }

                $carry->get($group)->push(...$plugins);

                return $carry;
            }, $this->config);
        
        return $this->boot();
    }

    /**
     * Register group into the existing configuration.
     *
     * @param array $plugins
     * @return Collection
     */
    public function registerGroup(string $group, array $plugins): Collection
    {
        return $this->register([
            $group => $plugins
        ]);
    }

    /**
     * Remove one or more plugins.
     *
     * @param array|mixed $subjects
     * @return Collection
     */
    public function unregister(array $subjects = []): Collection
    {
        $subjects = $this->configure($subjects);

        return $this->config = $this->config->map(function($plugins, $group) use ($subjects) {
            if(!$items = $subjects->get($group)) {
                return $plugins;
            }

            return $plugins->filter(function($plugin) use ($items) {
                foreach($items as $item) {
                    if($item->matches($plugin)) {
                        return false;
                    }
                }

                return true;
            });
        });
    }

    /**
     * Remove one or more groups.
     *
     * @param string|string[] ...$groups
     * @return Collection
     */
    public function unregisterGroup(...$groups): Collection
    {
        $groups = (new Collection($groups))->flatten()->map(function($key) {
            return ResourceFactory::resource($key);
        });

        return $this->config = $this->config->filter(
            function($plugins, $group) use ($groups) {
                return $groups->search($group) === false;
            }
        );
    }
}
