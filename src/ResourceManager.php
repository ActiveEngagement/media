<?php

namespace Actengage\Media;

use Actengage\Media\Contracts\Resource;
use Actengage\Media\Exceptions\InvalidResourceException;

class ResourceManager
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
    public function __construct(array $resources = [])
    {
        $this->configure($resources);
    }

    /**
     * Configures available resources.
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
     * Instantiates a resource instance.
     *
     * @param mixed $data
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
}
