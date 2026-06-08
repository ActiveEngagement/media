<?php

use Tests\Unit\Support\EventfulStub;

it('returns true when firing an event without a dispatcher', function (): void {
    $stub = new EventfulStub;

    expect($stub->triggerEvent('saving'))->toBeTrue();
});

it('returns early when flushing listeners without a dispatcher', function (): void {
    expect(EventfulStub::flushEventListeners())->toBeNull();
});

it('does nothing when registering an event without a dispatcher', function (): void {
    EventfulStub::triggerRegister('saving', fn () => null);

    expect(true)->toBeTrue();
});

it('sets and unsets the event dispatcher', function (): void {
    EventfulStub::setEventDispatcher(app('events'));

    expect(EventfulStub::getEventDispatcher())->not->toBeNull();

    EventfulStub::unsetEventDispatcher();

    expect(EventfulStub::getEventDispatcher())->toBeNull();
});
