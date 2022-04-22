<?php

namespace Tests\Unit\Plugins;

use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\HashFilename;
use Actengage\Media\Resources\Image;
use Tests\TestCase;

class HashFilenameTest extends TestCase
{
    public function testHashFilename()
    {
        Image::plugins([
            [HashFilename::class, [
                'length' => 8
            ]]
        ]);

        $resource = Resource::path(__DIR__.'/../../src/image.jpeg');
        
        $this->assertEquals(13, strlen($resource->filename));
    }
}