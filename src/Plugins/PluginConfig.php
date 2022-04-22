<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Plugin;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class PluginConfig
{
    /**
     * The plugin class name.
     *
     * @var string
     */
    protected string $class;
    
    /**
     * The options collection/
     *
     * @var Collection
     */
    protected Collection $options;

    /**
     * Determines if the config has been booted.
     *
     * @var boolean
     */
    protected bool $booted = false;

    /**
     * Create an instance of the config.
     *
     * @param array $plugin
     */
    public function __construct(array $plugin)
    {
        $this->class = Arr::get($plugin, 0);
        $this->options = new Collection(
            Arr::get($plugin, 1, [])
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
     *
     * @return boolean
     */
    public function booted(): bool
    {
        return $this->booted;
    }

    /**
     * Get the class property.
     *
     * @return string
     */
    public function class(): string
    {
        return $this->class;
    }

    /**
     * Get the options property.
     *
     * @return Collection
     */
    public function options(): Collection
    {
        return $this->options;
    }

    /**
     * Create an new instance of the plugin using the defined configuration.
     *
     * @return Plugin
     */
    public function plugin(): Plugin
    {
        return $this->class::make($this->options);
    }

    /**
     * Determines if this instance matches another instance.
     *
     * @param PluginConfig $plugin
     * @return boolean
     */
    public function matches(PluginConfig $plugin): bool
    {
        return $this->class === $plugin->class()
            && !$plugin->options()->diff($this->options)->count();
    }

    /**
     * Create an instance of the PluginConfig.
     *
     * @param array $config
     * @return PluginConfig
     */
    public static function make(PluginConfig|array $config): PluginConfig
    {
        if($config instanceof PluginConfig) {
            return $config;
        }

        return new static($config);
    }
}