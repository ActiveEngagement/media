<?php

declare(strict_types=1);

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Plugin as PluginInterface;
use Actengage\Media\Contracts\Resource;
use Actengage\Media\Media;
use Illuminate\Support\Collection;

/**
 * @phpstan-consistent-constructor
 */
abstract class Plugin implements PluginInterface
{
    /**
     * The resources that are ignored by the plugin.
     *
     * @var array<int, class-string<resource>>
     */
    protected static array $ignoreResources = [];

    /**
     * The only resources that are compatible with the plugin.
     *
     * @var array<int, class-string<resource>>
     */
    protected static array $compatibleResources = [];

    /**
     * Create an instance of a plugin.
     *
     * @param  Collection<array-key, mixed>  $options
     */
    public function __construct(
        /**
         * The plugin options.
         *
         * @var Collection<array-key, mixed>
         */
        protected Collection $options
    ) {}

    /**
     * Get the plugin options.
     *
     * @return Collection<array-key, mixed>
     */
    public function options(): Collection
    {
        return $this->options;
    }

    /**
     * Verify a resource against the compatible and ignored resources.
     */
    public function verifyResource(Resource $resource): bool
    {
        return $this->verifyCompatibleResources($resource)
            && $this->verifyIgnoredResources($resource);
    }

    /**
     * Verify a resource against the compatible resources.
     */
    protected function verifyCompatibleResources(Resource $resource): bool
    {
        if (! count($resources = static::compatibleResources())) {
            return true;
        }

        return in_array($resource::class, $resources);
    }

    /**
     * Verify a resource against the ignored resources.
     */
    protected function verifyIgnoredResources(Resource $resource): bool
    {
        if (! count($resources = static::ignoreResources())) {
            return true;
        }

        return ! in_array($resource::class, $resources);
    }

    /**
     * Initialize the plugin.
     *
     * @return void
     */
    public function initialized(Resource $resource)
    {
        //
    }

    /**
     * Runs before the `saving` event fires.
     *
     * @return void
     */
    public function beforeSaving(Resource $resource)
    {
        //
    }

    /**
     * Runs after the `saving` event fires.
     *
     * @return void
     */
    public function saving(Resource $resource, Media $model)
    {
        //
    }

    /**
     * Runs after the `saved` event fires.
     *
     * @return void
     */
    public function saved(Resource $resource, Media $model)
    {
        //
    }

    /**
     * Runs after the `storing` event fires.
     *
     * @return void
     */
    public function storing(Resource $resource, Media $model)
    {
        //
    }

    /**
     * Runs after the `stored` event fires.
     *
     * @return void
     */
    public function stored(Resource $resource, Media $model)
    {
        //
    }

    /**
     * Boot the plugin.
     *
     * @param  Collection<array-key, mixed>  $options
     * @return void
     */
    public static function boot(Collection $options)
    {
        //
    }

    /**
     * Get the resources that are ignored by the plugin.
     *
     * @return array<int, class-string<resource>>
     */
    public static function ignoreResources(): array
    {
        return static::$ignoreResources;
    }

    /**
     * Get the resources that are ignored by the plugin.
     *
     * @return array<int, class-string<resource>>
     */
    public static function compatibleResources(): array
    {
        return static::$compatibleResources;
    }

    /**
     * Create a new instance.
     *
     * @param  Collection<array-key, mixed>  $options
     */
    public static function make(Collection $options): static
    {
        return new static($options);
    }
}
