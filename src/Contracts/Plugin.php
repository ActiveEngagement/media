<?php

namespace Actengage\Media\Contracts;

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
    public function creating(Resource $resource);
    
    /**
     * Runs after the `created` event fires.
     *
     * @param Resource $resource
     * @return void
     */
    public function created(Resource $resource);

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