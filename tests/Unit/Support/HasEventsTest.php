<?php

use Actengage\Media\Resources\File;
use Actengage\Media\Resources\Resource;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Event;
use Tests\TestEvent;

it('dispatches custom and observable events', function (): void {
    Resource::setEventDispatcher(Event::fake());

    $class = new class extends File
    {
        protected $dispatchesEvents = [
            'initialized' => TestEvent::class,
        ];
    };

    $resource = new $class;
    $resource->initialize(__DIR__.'/../../src/file.txt');
    $resource->save();

    Event::assertDispatched($class::dispatchEventName('initialized'));
    Event::assertDispatched($class::dispatchEventName('saving'));
    Event::assertDispatched($class::dispatchEventName('saved'));
    Event::assertDispatched($class::dispatchEventName('storing'));
    Event::assertDispatched($class::dispatchEventName('stored'));
});

it('fires object-based custom events through the dispatcher', function (): void {
    $fired = false;

    Event::listen(TestEvent::class, function () use (&$fired) {
        $fired = true;

        return ['response'];
    });

    $class = new class extends File
    {
        protected $dispatchesEvents = [
            'initialized' => TestEvent::class,
        ];
    };

    $resource = new $class;
    $resource->initialize(__DIR__.'/../../src/file.txt');

    expect($fired)->toBeTrue();
    expect($resource)->toBeInstanceOf(File::class);
});

it('manages the observable event names', function (): void {
    $resource = new File;

    expect($resource->getObservableEvents())
        ->toContain('initialized', 'saving', 'saved', 'storing', 'stored');

    $resource->setObservableEvents(['custom']);
    expect($resource->getObservableEvents())->toContain('custom');

    $resource->addObservableEvents('another');
    expect($resource->getObservableEvents())->toContain('another');

    $resource->addObservableEvents(['x', 'y']);
    expect($resource->getObservableEvents())->toContain('x', 'y');

    $resource->removeObservableEvents('custom');
    expect($resource->getObservableEvents())->not->toContain('custom');

    $resource->removeObservableEvents(['x']);
    expect($resource->getObservableEvents())->not->toContain('x');
});

it('gets the event dispatcher instance', function (): void {
    expect(File::getEventDispatcher())->toBeInstanceOf(Dispatcher::class);
});
