<?php

namespace Tests\Unit\Plugins;

use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\HashDirectory;
use Actengage\Media\Resources\Image;
use Tests\TestCase;

class HashDirectoryTest extends TestCase
{
    public function testHashDirectory()
    {
        Image::register([
            [HashDirectory::class, [
                'length' => 8
            ]]
        ]);

        $resource = Resource::path(__DIR__.'/../../src/image.jpeg');
        
        $this->assertEquals(8, strlen($resource->directory));

        $resource = Resource::path(__DIR__.'/../../src/file.txt');
        
        $this->assertEquals(0, strlen($resource->directory));
    }
}