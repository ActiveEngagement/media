<?php

namespace Tests\Unit\Support;

use Actengage\Media\Resources\File;
use Actengage\Media\Resources\Image;
use Actengage\Media\Resources\Resource;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\TestEvent;

class HasEventsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Resource::setEventDispatcher(Event::fake());
    }

    public function testEventDispatcher()
    {
        $class = new class extends File {
            protected $dispatchesEvents = [
                'initialized' => TestEvent::class
            ];
        };

        $resource = new $class();
        $resource->initialize(__DIR__.'/../../src/file.txt');
        $resource->save();

        Event::assertDispatched($class::dispatchEventName('initialized'));
        Event::assertDispatched($class::dispatchEventName('saving'));
        Event::assertDispatched($class::dispatchEventName('saved'));
        Event::assertDispatched($class::dispatchEventName('storing'));
        Event::assertDispatched($class::dispatchEventName('stored'));
    }
}