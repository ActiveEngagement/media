<?php

declare(strict_types=1);

namespace Actengage\Media\Facades;

use Actengage\Media\ResourceFactory;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void boot()
 * @method static \Actengage\Media\ResourceFactory configure(array<array-key, mixed> $resources = [])
 * @method static bool is(\Actengage\Media\Contracts\Resource $resource, array<array-key, string>|string $keys)
 * @method static \Actengage\Media\Resources\Resource make(mixed $data)
 * @method static \Actengage\Media\Resources\Resource path(string $path)
 * @method static \Actengage\Media\Resources\Resource request(string $key)
 * @method static string resource(string $key)
 * @method static array<string, class-string<\Actengage\Media\Contracts\Resource>> resources()
 *
 * @see ResourceFactory
 */
class Resource extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ResourceFactory::class;
    }
}
