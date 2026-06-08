<?php

declare(strict_types=1);

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Resource;
use Illuminate\Support\Str;

/**
 * Hashes the resource filename using the sha1 algorithm.
 *
 * Available Options:
 *
 * - `length`: The length of the hash. Defaults to 8.
 */
class HashFilename extends Plugin
{
    /**
     * Fires after the resource has been initialized.
     *
     * @return void
     */
    public function initialized(Resource $resource)
    {
        $option = $this->options->get('length', 8);

        $length = max(6, min(is_numeric($option) ? (int) $option : 8, 40));

        $resource->filename(
            substr(sha1(microtime().Str::random(8)), 0, $length)
        );
    }
}
