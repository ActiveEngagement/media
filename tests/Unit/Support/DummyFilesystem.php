<?php

namespace Tests\Unit\Support;

class DummyFilesystem
{
    /**
     * @var array<array-key, mixed>|null
     */
    public ?array $options;

    /**
     * @param  resource  $resource
     * @param  array<array-key, mixed>  $options
     */
    public function writeStream(string $path, $resource, array $options = []): bool
    {
        $this->options = $options;

        return true;
    }
}
