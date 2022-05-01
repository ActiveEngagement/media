<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Plugin as PluginInterface;
use Actengage\Media\Contracts\Resource;
use Actengage\Media\Media;
use Illuminate\Support\Collection;

abstract class Plugin implements PluginInterface
{
    /**
     * The resources that are ignored by the plugin.
     *
     * @var array
     */
    protected static array $ignoreResources = [];

    /**
     * The only resources that are compatible with the plugin.
     *
     * @var array
     */
    protected static array $compatibleResources = [];

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
     * Verify a resource against the compatible and ignored resources.
     *
     * @return boolean
     */
    public function verifyResource(Resource $resource): bool
    {
        return $this->verifyCompatibleResources($resource)
            && $this->verifyIgnoredResources($resource);
    }
    
    /**
     * Verify a resource against the compatible resources.
     *
     * @return boolean
     */
    protected function verifyCompatibleResources(Resource $resource): bool
    {
        if(!count($resources = static::compatibleResources())) {
            return true;
        }

        return in_array(get_class($resource), $resources);
    }
    
    /**
     * Verify a resource against the ignored resources.
     *
     * @return boolean
     */
    protected function verifyIgnoredResources(Resource $resource): bool
    {
        if(!count($resources = static::ignoreResources())) {
            return true;
        }

        return !in_array(get_class($resource), $resources);
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
     * Runs before the `saving` event fires.
     *
     * @param Resource $resource
     * @return void
     */
    public function beforeSaving(Resource $resource)
    {
        //
    }
    
    /**
     * Runs after the `saving` event fires.
     *
     * @param Resource $resource
     * @param Media $model
     * @return void
     */
    public function saving(Resource $resource, Media $model)
    {
        //
    }
    
    /**
     * Runs after the `saved` event fires.
     *
     * @param Resource $resource
     * @param Media $model
     * @return void
     */
    public function saved(Resource $resource, Media $model)
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
     * Get the resources that are ignored by the plugin.
     *
     * @return array
     */
    public static function ignoreResources(): array
    {
        return static::$ignoreResources;
    }

    /**
     * Get the resources that are ignored by the plugin.
     *
     * @return array
     */
    public static function compatibleResources(): array
    {
        return static::$compatibleResources;
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