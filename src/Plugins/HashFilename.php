<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Resource;
use Illuminate\Support\Str;

class HashFilename extends Plugin
{
    /**
     * Fires after the resource has been initialized.
     *
     * @param Resource $resource
     * @return void
     */
    public function initialized(Resource $resource)
    {
        $length = (int) max(6, min($this->options->get('length', 8), 40));

        $resource->filename(
            substr(sha1(microtime().Str::random(8)), 0, $length)
        );
    }
}