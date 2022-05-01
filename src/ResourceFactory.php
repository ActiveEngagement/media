<?php

namespace Actengage\Media;

use Actengage\Media\Contracts\Resource;
use Actengage\Media\Exceptions\InvalidResourceException;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;

class ResourceFactory
{
    /**
     * An array of resource classes.
     *
     * @var array
     */
    protected array $resources = [];

    /**
     * Creates new instance of Resource Manager.
     *
     * @param array $config
     */
    public function __construct(Application $app)
    {
        $this->configure($app->config['media.resources']);
    }

    /**
     * Configure the resources.
     *
     * @param array $resources
     * @return self
     */
    public function configure(array $resources = []): self
    {
        $this->resources = array_replace($this->resources, $resources);

        return $this;
    }

    /**
     * Check if a resource instance is one of the given keys.
     *
     * @param Resource $resource
     * @param array|string $keys
     * @return boolean
     */
    public function is(Resource $resource, array|string $keys): bool
    {
        foreach(Arr::wrap($keys) as $key) {
            if(get_class($resource) === Arr::get($this->resources, $key, get_class($resource))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Instantiates a resource instance.
     *
     * @param mixed $data
     * @throws InvalidResourceException
     * @return \Actengage\Media\Contracts\Resource
     */
    public function make($data): Resource
    {
        foreach ($this->resources as $resource) {
            try {
                return $resource::make($data);
            } catch (InvalidResourceException $e) {
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
     * @param string $path
     * @param string $filename
     * @return \Actengage\Media\Contracts\Resource
     */
    public function path(string $path)
    {
        return $this->make($path);
    }

    /**
     * Instantiates a resource instance using a request file.
     *
     * @param string $key
     * @return \Actengage\Media\Contracts\Resource
     */
    public function request(string $key)
    {
        return $this->make(request($key));
    }

    /**
     * Get the resource by key.
     *
     * @param string $key
     * @return string
     */
    public function resource(string $key): string
    {
        if(is_a($key, Resource::class, true)) {
            return $key;
        }

        return Arr::get($this->resources(), $key, $key);
    }

    /**
     * Get the registered resources.
     *
     * @return array
     */
    public function resources(): array
    {
        return $this->resources;
    }
}
