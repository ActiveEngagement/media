<?php

declare(strict_types=1);

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Resource;
use Illuminate\Support\Str;

/**
 * Hashes the resource directory using the sha1 algorithm.
 *
 * Available Options:
 *
 * - `length`: The length of the hash. Defaults to 8.
 */
class HashDirectory extends Plugin
{
    /**
     * Fires after the resource has been initialized.
     *
     * @return void
     */
    public function initialized(Resource $resource)
    {
        $option = $this->options->get('length', 40);

        $length = max(6, min(is_numeric($option) ? (int) $option : 40, 40));

        $resource->directory(
            substr(sha1(microtime().Str::random(8)), 0, $length)
        );
    }
}
