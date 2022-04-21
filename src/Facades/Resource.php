<?php

namespace Actengage\Media\Facades;

use Actengage\Media\ResourceFactory;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Actengage\Media\ResourceFactory configure(array $resources = []) Configures available resources.
 * @method static \Actengage\Media\Contracts\Resource make($data) Instantiates a resource instance.
 * @see \Actengage\Media\ResourceFactory
 */
class Resource extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ResourceFactory::class;
    }
}