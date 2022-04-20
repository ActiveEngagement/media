<?php

namespace Actengage\Media\Contracts;

use Illuminate\Support\Collection;

interface Plugin
{
    /**
     * Boot the plugin.
     *
     * @param Collection $options
     * @return void
     */
    public static function boot(Collection $options): void;
}