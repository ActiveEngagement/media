<?php

namespace Tests\Unit\Resources;

use Actengage\Media\Contracts\Resource as ContractsResource;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Resources\Image;
use Tests\TestCase;

class ResourceTest extends TestCase
{
    public function testResourceIsImage()
    {
        $image = 0;
        $file = 0;

        Resource::path(__DIR__.'/../../src/image.jpeg')
            ->is(Image::class, function($resource) use (&$image) {
                $this->assertInstanceOf(ContractsResource::class, $resource);
                
                $image++;
            })
            ->is('file', function() use ($file) {
                $file++;
            })
            ->is(['image', 'file'], function($resource) use (&$image, &$file) {
                $image++;
                $file++;
            });
        
        $this->assertEquals(2, $image);
        $this->assertEquals(1, $file);
    }

    public function testResourceWhenTruthy()
    {
        $truths = 0;

        Resource::path(__DIR__.'/../../src/image.jpeg')
            ->when(true, function($resource) use (&$truths) {
                $this->assertInstanceOf(ContractsResource::class, $resource);

                $truths++;
            })
            ->when(function($resource) {
                $this->assertInstanceOf(ContractsResource::class, $resource);

                return true;
            }, function() use (&$truths) {
                $truths++;
            })
            ->when(false, function() use (&$truths) {
                $truths++;
            })
            ->when(function() {
                return false;
            }, function() use (&$truths) {
                $truths++;
            });
        
        $this->assertEquals(2, $truths);
    }

    public function testResourceWhenFalsy()
    {
        $falsy = 0;

        Resource::path(__DIR__.'/../../src/image.jpeg')
            ->not(false, function($resource) use (&$falsy) {
                $this->assertInstanceOf(ContractsResource::class, $resource);

                $falsy++;
            })
            ->not(function($resource) {
                $this->assertInstanceOf(ContractsResource::class, $resource);

                return false;
            }, function() use (&$falsy) {
                $falsy++;
            })
            ->not(true, function() use (&$falsy) {
                $falsy++;
            })
            ->not(function() {
                return true;
            }, function() use (&$falsy) {
                $falsy++;
            });
        
        $this->assertEquals(2, $falsy);
    }

    public function testDatabaseAttributes()
    {
        $resource = Resource::path(__DIR__.'/../../src/file.txt')
            ->disk($disk = 'public')
            ->directory($directory = 'a/b/c')
            ->context($context = 'testing')
            ->caption($caption = 'This is a text file used for testing.')
            ->title($title = 'This is a test!')
            ->meta([
                'a' => 1,
                'b' => 2
            ])
            ->meta('c', 3)
            ->tags('a', ['b'])
            ->tags(['c', 'd']);        

        $this->assertEquals($disk, $resource->disk);
        $this->assertEquals($directory, $resource->directory);
        $this->assertEquals($context, $resource->context);
        $this->assertEquals($caption, $resource->caption);
        $this->assertEquals($title, $resource->title);
        $this->assertEquals(['a', 'b', 'c', 'd'], $resource->tags->all());
        $this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3], $resource->meta->all());

        $model = $resource->save();

        $this->assertEquals($disk, $model->disk);
        $this->assertEquals($directory, $model->directory);
        $this->assertEquals($context, $model->context);
        $this->assertEquals($caption, $model->caption);
        $this->assertEquals($title, $model->title);
        $this->assertEquals(['a', 'b', 'c', 'd'], $model->tags->all());
        $this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3], $model->meta->all());
    }

    public function testChangeFilename()
    {
        $model = Resource::path(__DIR__.'/../../src/file.txt')
            ->filename('renamed.html')
            ->mime('plain/html')
            ->save();
        
        $this->assertEquals('plain/html', $model->mime);
        $this->assertEquals('html', $model->extension);
        $this->assertEquals('renamed.html', $model->filename);
    }
    
    public function testChangeExtension()
    {
        $model = Resource::path(__DIR__.'/../../src/file.txt')
            ->extension('html')
            ->mime('plain/html')
            ->save();
        
        $this->assertEquals('plain/html', $model->mime);
        $this->assertEquals('html', $model->extension);
        $this->assertEquals('file.html', $model->filename);
    }

    public function testChangeFilenameWithoutChanginTheExtension()
    {
        $model = Resource::path(__DIR__.'/../../src/file.txt')
            ->filename('renamed')
            ->save();
        
        $this->assertEquals('txt', $model->extension);
        $this->assertEquals('renamed.txt', $model->filename);
    }

    public function testChangeExtensionWithoutChangingTheFilename()
    {
        $model = Resource::path(__DIR__.'/../../src/file.txt')
            ->extension('html')
            ->save();
        
        $this->assertEquals('html', $model->extension);
        $this->assertEquals('file.html', $model->filename);
    }

    public function testParentAttribute()
    {
        $parent = Resource::path(__DIR__.'/../../src/file.txt')
            ->filename('parent.txt')
            ->save();
        
        $child = Resource::path(__DIR__.'/../../src/file.txt')
            ->filename('child.txt')
            ->parent($parent)
            ->save();        
        
        $this->assertNull($parent->parent);
        $this->assertEquals($child->parent, $parent);
        $this->assertCount(1, $parent->children);
    }

    public function testFormattedFilesizeAttribute()
    {
        $model = Resource::path(__DIR__.'/../../src/index.html')->save();
        
        $this->assertEquals('286 B', $model->size);
    }

    public function testInitializingWithoutData()
    {
        $resource = new Image();
        $resource->initialize(__DIR__.'/../../src/image.jpeg');
        
        $this->assertTrue($resource->save()->exists);
    }

    public function testTagsCorrectlySetsTaggingStorageOption()
    {
        $resource = Resource::path(__DIR__.'/../../src/file.txt')
            ->tags(['a', 'b', 'c']);
        
        $this->assertEquals(
            'a=true&b=true&c=true',
            $resource->storageOptions->get('Tagging')
        );
    }

}