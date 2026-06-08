<?php

namespace Actengage\Media\Support;

use Actengage\Media\Exceptions\BadAttributeException;

trait Attributes
{
    /**
     * Fluently call the attribute method.
     *
     * @param  string  $name
     * @param  array<array-key, mixed>  $arguments
     */
    public function __call($name, $arguments): mixed
    {
        return $this->attribute($name, ...$arguments);
    }

    /**
     * Fluently get or set the attribute.
     *
     * @param  mixed  ...$args
     */
    public function attribute(string $key, ...$args): mixed
    {
        if (! count($args)) {
            return $this->getAttribute($key);
        }

        return $this->setAttribute($key, ...$args);
    }

    /**
     * Get the attribute.
     *
     * @throws BadAttributeException
     */
    public function getAttribute(string $key): mixed
    {
        if (! property_exists($this, $key)) {
            throw new BadAttributeException(static::class, $key);
        }

        return $this->$key;
    }

    /**
     * Set the attribute.
     *
     * @throws BadAttributeException
     */
    public function setAttribute(string $key, mixed $value): self
    {
        if (! property_exists($this, $key)) {
            throw new BadAttributeException(static::class, $key);
        }

        $this->$key = $value;

        return $this;
    }
}
