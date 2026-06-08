<?php

namespace Tests\Unit\Support;

use Actengage\Media\Support\HasEvents;
use Closure;

/**
 * A bare class using the HasEvents trait with no dispatcher configured, used to
 * exercise the trait's "missing dispatcher" guards.
 */
final class EventfulStub
{
    use HasEvents;

    public function triggerEvent(string $event): mixed
    {
        return $this->fireEvent($event);
    }

    public static function triggerRegister(string $event, Closure $callback): void
    {
        self::registerEvent($event, $callback);
    }
}
