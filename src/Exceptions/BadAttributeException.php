<?php

namespace Actengage\Media\Exceptions;

use Error;

class BadAttributeException extends Error
{
    public function __construct(string $class, string $attribute)
    {
        parent::__construct(sprintf(
            '%s::$%s does not exist', $class, $attribute
        ));
    }
}