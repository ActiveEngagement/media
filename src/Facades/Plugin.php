<?php

namespace Actengage\Media\Facades;

use Actengage\Media\PluginFactory;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Actengage\Media\PluginFactory boot() Boot the plugins.
 * @method static \Illuminate\Support\Collection initialized() Initialize the plugins.
 * @see \Actengage\Media\PluginFactory
 */
class Plugin extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PluginFactory::class;
    }
}