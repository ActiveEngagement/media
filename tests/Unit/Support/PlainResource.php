<?php

namespace Tests\Unit\Support;

use Actengage\Media\Data\Stream;
use Actengage\Media\Media;
use Actengage\Media\Resources\Resource;
use Psr\Http\Message\StreamInterface;

/**
 * A bare resource that does not override initialize(), used to exercise the
 * abstract Resource base behavior.
 */
class PlainResource extends Resource
{
    public function store(Media $model): bool
    {
        return true;
    }

    public function stream(): StreamInterface
    {
        return Stream::make('plain', [
            'metadata' => ['filename' => 'plain.txt'],
        ]);
    }
}
