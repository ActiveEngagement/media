<?php

namespace Tests\Unit\Plugins;

use Actengage\Media\Media;

class PrimaryKeyExtractor
{
    public function __invoke(Media $model): mixed
    {
        return $model->getKey();
    }
}
