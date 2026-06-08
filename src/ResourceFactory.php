<?php

namespace Actengage\Media;

use Actengage\Media\Contracts\Resource;
use Actengage\Media\Exceptions\InvalidResourceException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class ResourceFactory
{
    /**
     * An array of resource classes.
     *
     * @var array<string, class-string<resource>>
     */
    protected array $resources = [];

    /**
     * Creates new instance of Resource Manager.
     */
    public function __construct()
    {
        $this->configure(Config::array('media.resources', []));
    }

    /**
     * Run the `boot()` methods on the applicable resources.
     */
    public function boot(): void
    {
        foreach ($this->resources as $resource) {
            $resource::boot();
        }
    }

    /**
     * Configure the resources.
     *
     * @param  array<array-key, mixed>  $resources
     */
    public function configure(array $resources = []): self
    {
        foreach ($resources as $key => $resource) {
            if (is_string($resource) && is_a($resource, Resource::class, true)) {
                $this->resources[(string) $key] = $resource;
            }
        }

        return $this;
    }

    /**
     * Check if a resource instance is one of the given keys.
     *
     * @param  array<array-key, string>|string  $keys
     */
    public function is(Resource $resource, array|string $keys): bool
    {
        foreach (Arr::wrap($keys) as $key) {
            if ($resource::class === Arr::get($this->resources, $key, $resource::class)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Instantiates a resource instance.
     *
     * @param  mixed  $data
     *
     * @throws InvalidResourceException
     */
    public function make($data): Resource
    {
        foreach ($this->resources as $resource) {
            try {
                return $resource::make($data);
            } catch (InvalidResourceException) {
                continue;
            }
        }

        throw new InvalidResourceException(
            'A resource cannot be created from the given input.'
        );
    }

    /**
     * Instantiates a resource instance using a file path.
     *
     * @return Contracts\Resource
     */
    public function path(string $path)
    {
        return $this->make($path);
    }

    /**
     * Instantiates a resource instance using a request file.
     *
     * @return Contracts\Resource
     */
    public function request(string $key)
    {
        return $this->make(request($key));
    }

    /**
     * Get the resource by key.
     */
    public function resource(string $key): string
    {
        if (is_a($key, Resource::class, true)) {
            return $key;
        }

        $resource = Arr::get($this->resources(), $key, $key);

        return is_string($resource) ? $resource : $key;
    }

    /**
     * Get the registered resources.
     *
     * @return array<string, class-string<resource>>
     */
    public function resources(): array
    {
        return $this->resources;
    }
}
