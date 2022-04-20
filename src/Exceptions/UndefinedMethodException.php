<?php

namespace Actengage\Media\Exceptions;

use Error;

class UndefinedMethodException extends Error
{
    public function __construct(string $class, string $method)
    {
        parent::__construct(sprintf(
            'Call to undefined method %s::%s()', $class, $method
        ));
    }
}