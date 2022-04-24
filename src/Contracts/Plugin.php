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
     * Runs after the `saving` event fires.
     *
     * @param Resource $resource
     * @return void
     */
    public function saving(Resource $resource, Media $model);
    
    /**
     * Runs after the `saved` event fires.
     *
     * @param Resource $resource
     * @param Media $model
     * @return void
     */
    public function saved(Resource $resource, Media $model);
    
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