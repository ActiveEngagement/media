<?php

namespace Tests\Unit\Support;

use Actengage\Media\Resources\Image;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class HasEventsTest extends TestCase
{
    public function testEventDispatcher()
    {
        $creating = 0;
        $created = 0;

        $file = new UploadedFile(
            __DIR__.'/../../src/image.jpeg', 'image.jpeg'
        );

        Image::creating(function() use (&$creating) {
            $creating++;
        });

        Image::created(function() use (&$created) {
            $created++;
        });

        $resource = (new Image($file))
            ->creating(function() use (&$creating) {
                $creating++;
            })
            ->created(function() use (&$created) {
                $created++;
            });
        
        $resource->save();
        
        Image::flushEventListeners();

        $resource->save();

        $this->assertEquals(2, $creating);
        $this->assertEquals(2, $created);
    }
}