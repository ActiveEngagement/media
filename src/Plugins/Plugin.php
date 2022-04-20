<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Plugin as PluginInterface;
use Illuminate\Support\Collection;

abstract class Plugin implements PluginInterface {

    /**
     * Has the plugin been booted.
     *
     * @var boolean
     */
    public static $booted = false;

    /**
     * Boot the plugin.
     *
     * @param Collection $options
     * @return void
     */
    public static function boot(Collection $options): void
    {
        //
    }

}