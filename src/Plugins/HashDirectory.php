<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Resource;
use Illuminate\Support\Str;

/**
 * Hashes the resource directory using the sha1 algorithm.
 * 
 * Available Options:
 * 
 * @var mixed $length The length of the hash. Defaults to 8.
 */
class HashDirectory extends Plugin
{
    /**
     * Fires after the resource has been initialized.
     *
     * @param Resource $resource
     * @return void
     */
    public function initialized(Resource $resource)
    {
        $length = (int) max(6, min($this->options->get('length', 40), 40));

        $resource->directory(
            substr(sha1(microtime().Str::random(8)), 0, $length)
        );       
    }
}