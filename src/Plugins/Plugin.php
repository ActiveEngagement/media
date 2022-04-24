<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Plugin as PluginInterface;
use Actengage\Media\Contracts\Resource;
use Actengage\Media\Media;
use Illuminate\Support\Collection;

abstract class Plugin implements PluginInterface
{
    /**
     * The plugin options.
     *
     * @var Collection
     */
    protected Collection $options;

    /**
     * Create an instance of a plugin.
     *
     * @param Collection $options
     */
    public function __construct(Collection $options)
    {
        $this->options = $options;
    }

    /**
     * Get the plugin options.
     *
     * @return Collection
     */
    public function options(): Collection
    {
        return $this->options;
    }

    /**
     * Initialize the plugin.
     *
     * @param Resource $resource
     * @return void
     */
    public function initialized(Resource $resource)
    {
        //
    }
    
    /**
     * Runs after the `creating` event fires.
     *
     * @param Resource $resource
     * @param Media $model
     * @return void
     */
    public function creating(Resource $resource, Media $model)
    {
        //
    }
    
    /**
     * Runs after the `created` event fires.
     *
     * @param Resource $resource
     * @param Media $model
     * @return void
     */
    public function created(Resource $resource, Media $model)
    {
        //
    }
    
    /**
     * Runs after the `storing` event fires.
     *
     * @param Resource $resource
     * @param Media $model
     * @return void
     */
    public function storing(Resource $resource, Media $model)
    {
        //
    }
    
    /**
     * Runs after the `stored` event fires.
     *
     * @param Resource $resource
     * @param Media $model
     * @return void
     */
    public function stored(Resource $resource, Media $model)
    {
        //
    }

    /**
     * Boot the plugin.
     *
     * @param Collection $options
     * @return void
     */
    public static function boot(Collection $options)
    {
        //
    }

    /**
     * Create a new instance.
     *
     * @param Collection $options
     * @return static
     */
    public static function make(Collection $options): static
    {
        return new static($options);
    }

}