<?php

namespace Tests\Unit\Support;

use League\Flysystem\Local\LocalFilesystemAdapter;

class DummyFilesystem
{
    public ?array $options;

    public function writeStream($path, $resource, array $options = [])
    {
        $this->options = $options;

        return true;
    }
}