<?php

namespace Tests\Unit\Support;

use Actengage\Media\Resources\Image;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class HasEventsTest extends TestCase
{
    public function testEventDispatcher()
    {
        $saving = 0;
        $saved = 0;

        $file = new UploadedFile(
            __DIR__.'/../../src/image.jpeg', 'image.jpeg'
        );

        Image::saving(function() use (&$saving) {
            $saving++;
        });

        Image::saved(function() use (&$saved) {
            $saved++;
        });

        $resource = (new Image($file))
            ->saving(function() use (&$saving) {
                $saving++;
            })
            ->saved(function() use (&$saved) {
                $saved++;
            });
        
        $resource->save();
        
        Image::flushEventListeners();

        $resource->save();

        $this->assertEquals(2, $saving);
        $this->assertEquals(2, $saved);
    }
}