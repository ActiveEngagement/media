<?php

namespace Tests\Unit;

use Actengage\Media\Facades\Resource;
use Actengage\Media\Resources\File;
use Actengage\Media\Resources\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ResourceFactoryTest extends TestCase
{
    public function testConfiguringTheFactory()
    {
        Resource::configure(Config::get('media.resources'));

        $this->assertEquals(Image::class, Resource::resource('image'));
        $this->assertEquals(File::class, Resource::resource('file'));
        $this->assertNull(Resource::resource('test'));
    }

    public function testCreateFromRequest()
    {
        $file = new UploadedFile(
            __DIR__.'/../src/image.jpeg', 'image.jpeg'
        );
        
        request()->files->set('image', $file);

        $resource = Resource::request('image');

        $this->assertInstanceOf(Image::class, $resource);
    }

    public function testCreateFromPath()
    {
        $resource = Resource::path(
            __DIR__.'/../src/image.jpeg'
        );

        $this->assertInstanceOf(Image::class, $resource);
        $this->assertEquals('image.jpeg', $resource->filename);
    }
}