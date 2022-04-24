<?php

namespace Actengage\Media\Contracts;

use Actengage\Media\Media;
use Illuminate\Support\Collection;

interface Plugin
{
    /**
     * Initialize the plugin.
     *
     * @param Resource $resource
     * @return void
     */
    public function initialized(Resource $resource);
    
    /**
     * Runs after the `creating` event fires.
     *
     * @param Resource $resource
     * @return void
     */
    public function creating(Resource $resource, Media $model);
    
    /**
     * Runs after the `created` event fires.
     *
     * @param Resource $resource
     * @param Media $model
     * @return void
     */
    public function created(Resource $resource, Media $model);
    
    /**
     * Runs after the `storing` event fires.
     *
     * @param Resource $resource
     * @param Media $model
     * @return void
     */
    public function storing(Resource $resource, Media $model);
    
    /**
     * Runs after the `stored` event fires.
     *
     * @param Resource $resource
     * @param Media $model
     * @return void
     */
    public function stored(Resource $resource, Media $model);

    /**
     * Get the plugin options.
     *
     * @return Collection
     */
    public function options(): Collection;

    /**
     * Boot the plugin.
     *
     * @param Collection $options
     * @return void
     */
    public static function boot(Collection $options);
}