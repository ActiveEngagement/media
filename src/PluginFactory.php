<?php

namespace Actengage\Media;

use Actengage\Media\Contracts\Plugin;
use Actengage\Media\Contracts\Resource;
use Actengage\Media\Facades\Resource as ResourceFactory;
use Actengage\Media\Plugins\PluginConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class PluginFactory
{
    /**
     * An array of plugins
     *
     * @var Collection<string, Collection<int, PluginConfig>>
     */
    protected Collection $config;

    /**
     * Create an instance of the plugin factory.
     */
    public function __construct()
    {
        $this->config = $this->configure(Config::array('media.plugins', []));
    }

    /**
     * Run the `boot()` methods on the applicable plugins.
     *
     * @return Collection<string, Collection<int, PluginConfig>>
     */
    public function boot(): Collection
    {
        return $this->config->each(fn (Collection $plugins) => $plugins->where(fn (PluginConfig $plugin) => ! $plugin->booted())->each(fn (PluginConfig $plugin) => $plugin->boot()));
    }

    /**
     * Get the configuration.
     *
     * @return Collection<string, Collection<int, PluginConfig>>
     */
    public function config(): Collection
    {
        return $this->config;
    }

    /**
     * Configure the plugins.
     *
     * @param  array<array-key, mixed>  $plugins
     * @return Collection<string, Collection<int, PluginConfig>>
     */
    public function configure(array $plugins): Collection
    {
        return (new Collection($plugins))
            ->groupBy(fn ($subject, $key) => is_numeric($key) ? 'global' : ResourceFactory::resource((string) $key))
            ->map(fn (Collection $group, $key) => $key == 'global' ? $group : $group->flatten(1))
            ->map(fn (Collection $group) => $group->map(fn ($plugin) => PluginConfig::make(
                $plugin instanceof PluginConfig || is_array($plugin) || is_string($plugin) ? $plugin : []
            ))->values());
    }

    /**
     * Flush all the plugins from the config.
     *
     * @return Collection<string, Collection<int, PluginConfig>>
     */
    public function flush(): Collection
    {
        return $this->config = new Collection;
    }

    /**
     * Initialize the plugins.
     *
     * @return Collection<int, Plugin>
     */
    public function initialize(Resource $resource): Collection
    {
        return $this->config->only('global', $resource::class)
            ->flatten(1)
            ->map(fn (PluginConfig $config) => $config->plugin())
            ->values();
    }

    /**
     * Register plugins into the existing configuration.
     *
     * @param  array<array-key, mixed>  $plugins
     * @return Collection<string, Collection<int, PluginConfig>>
     */
    public function register(array $plugins): Collection
    {
        $this->configure($plugins)
            ->reduce(function (Collection $carry, Collection $plugins, string $group) {
                if (! $carry->has($group)) {
                    $carry->put($group, new Collection);
                }

                $group = $carry->get($group);

                if ($group instanceof Collection) {
                    $group->push(...$plugins);
                }

                return $carry;
            }, $this->config);

        return $this->boot();
    }

    /**
     * Register group into the existing configuration.
     *
     * @param  array<array-key, mixed>  $plugins
     * @return Collection<string, Collection<int, PluginConfig>>
     */
    public function registerGroup(string $group, array $plugins): Collection
    {
        return $this->register([
            $group => $plugins,
        ]);
    }

    /**
     * Remove one or more plugins.
     *
     * @param  array<array-key, mixed>  $subjects
     * @return Collection<string, Collection<int, PluginConfig>>
     */
    public function unregister(array $subjects = []): Collection
    {
        $subjects = $this->configure($subjects);

        return $this->config = $this->config->map(function (Collection $plugins, string $group) use ($subjects) {
            if (! $items = $subjects->get($group)) {
                return $plugins;
            }

            return $plugins->filter(function (PluginConfig $plugin) use ($items) {
                foreach ($items as $item) {
                    if ($item->matches($plugin)) {
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
     * @param  string|string[]  ...$groups
     * @return Collection<string, Collection<int, PluginConfig>>
     */
    public function unregisterGroup(...$groups): Collection
    {
        $groups = (new Collection($groups))->flatten()->map(fn ($key) => ResourceFactory::resource(is_scalar($key) ? (string) $key : ''));

        return $this->config = $this->config->filter(
            fn (Collection $plugins, string $group) => $groups->search($group) === false
        );
    }
}
