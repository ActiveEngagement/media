<?php

namespace Actengage\Media\Facades;

use Actengage\Media\Media as Model;
use Illuminate\Support\Facades\Facade;

class Media extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Model::class;
    }
}