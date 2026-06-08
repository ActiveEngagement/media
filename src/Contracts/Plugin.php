<?php

declare(strict_types=1);

namespace Actengage\Media\Contracts;

use Actengage\Media\Media;
use Illuminate\Support\Collection;

interface Plugin
{
    /**
     * Verify a resource against the compatible and ignored resources.
     */
    public function verifyResource(Resource $resource): bool;

    /**
     * Initialize the plugin.
     *
     * @return void
     */
    public function initialized(Resource $resource);

    /**
     * Runs before the `saving` event fires.
     *
     * @return void
     */
    public function beforeSaving(Resource $resource);

    /**
     * Runs after the `saving` event fires.
     *
     * @return void
     */
    public function saving(Resource $resource, Media $model);

    /**
     * Runs after the `saved` event fires.
     *
     * @return void
     */
    public function saved(Resource $resource, Media $model);

    /**
     * Runs after the `storing` event fires.
     *
     * @return void
     */
    public function storing(Resource $resource, Media $model);

    /**
     * Runs after the `stored` event fires.
     *
     * @return void
     */
    public function stored(Resource $resource, Media $model);

    /**
     * Get the plugin options.
     *
     * @return Collection<array-key, mixed>
     */
    public function options(): Collection;

    /**
     * Boot the plugin.
     *
     * @param  Collection<array-key, mixed>  $options
     * @return void
     */
    public static function boot(Collection $options);

    /**
     * Create a new instance of the plugin.
     *
     * @param  Collection<array-key, mixed>  $options
     */
    public static function make(Collection $options): self;
}
