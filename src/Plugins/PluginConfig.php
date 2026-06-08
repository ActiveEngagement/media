<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Plugin;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * @phpstan-consistent-constructor
 */
class PluginConfig
{
    /**
     * The plugin class name.
     *
     * @var class-string<Plugin>
     */
    protected string $class;

    /**
     * The options collection/
     *
     * @var Collection<array-key, mixed>
     */
    protected Collection $options;

    /**
     * Determines if the config has been booted.
     */
    protected bool $booted = false;

    /**
     * Create an instance of the config.
     *
     * @param  array<array-key, mixed>|string  $plugin
     */
    public function __construct(array|string $plugin)
    {
        if (is_string($plugin)) {
            $plugin = [$plugin];
        }

        $class = Arr::get($plugin, 0);

        $this->class = is_string($class) && is_a($class, Plugin::class, true) ? $class : throw new \InvalidArgumentException('A plugin config must reference a valid plugin class.');

        $options = Arr::get($plugin, 1, []);

        $this->options = new Collection(
            is_array($options) ? $options : [$options]
        );
    }

    /**
     * Call the static boot method on the class.
     *
     * @return void
     */
    public function boot()
    {
        $this->class::boot($this->options);
        $this->booted = true;
    }

    /**
     * Get the booted property.
     */
    public function booted(): bool
    {
        return $this->booted;
    }

    /**
     * Get the class property.
     *
     * @return class-string<Plugin>
     */
    public function class(): string
    {
        return $this->class;
    }

    /**
     * Get the options property.
     *
     * @return Collection<array-key, mixed>
     */
    public function options(): Collection
    {
        return $this->options;
    }

    /**
     * Create an new instance of the plugin using the defined configuration.
     */
    public function plugin(): Plugin
    {
        return $this->class::make($this->options);
    }

    /**
     * Determines if this instance matches another instance.
     */
    public function matches(PluginConfig $plugin): bool
    {
        return $this->class === $plugin->class()
            && ! $plugin->options()->diff($this->options)->count();
    }

    /**
     * Create an instance of the PluginConfig.
     *
     * @param  PluginConfig|array<array-key, mixed>|string  $config
     */
    public static function make(PluginConfig|array|string $config): PluginConfig
    {
        if ($config instanceof PluginConfig) {
            return $config;
        }

        return new static($config);
    }
}
