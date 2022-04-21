<?php

namespace Actengage\Media\Support;

use Actengage\Media\Exceptions\BadAttributeException;

trait Attributes
{
    /**
     * Fluently call the attribute method.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments): mixed
    {
        return $this->attribute($name, ...$arguments);
    }

    /**
     * Fluently get or set the attribute.
     *
     * @param string $key
     * @param array ...$args
     * @return mixed
     */
    public function attribute(string $key, ...$args): mixed
    {
        if(!count($args)) {
            return $this->getAttribute($key);
        }

        return $this->setAttribute($key, ...$args);
    }

    /**
     * Get the attribute.
     *
     * @param string $key
     * @throws BadAttributeException
     * @return mixed
     */
    public function getAttribute(string $key): mixed
    {
        if(!property_exists($this, $key)) {
            throw new BadAttributeException(static::class, $key);
        }

        return $this->$key;
    }
    
    /**
     * Set the attribute.
     *
     * @param string $key
     * @param mixed $value
     * @throws BadAttributeException
     * @return self
     */
    public function setAttribute(string $key, mixed $value): self
    {
        if(!property_exists($this, $key)) {
            throw new BadAttributeException(static::class, $key);
        }

        $this->$key = $value;

        return $this;
    }
}