<?php

namespace Tests\Unit\Resources;

use Actengage\Media\Facades\Resource;
use Tests\TestCase;

class ResourceTest extends TestCase
{
    public function testDatabaseAttributes()
    {
        $resource = Resource::make(__DIR__.'/../../src/file.txt')
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
        $model = Resource::make(__DIR__.'/../../src/file.txt')
            ->filename('renamed.html')
            ->mime('plain/html')
            ->save();
        
        $this->assertEquals('plain/html', $model->mime);
        $this->assertEquals('html', $model->extension);
        $this->assertEquals('renamed.html', $model->filename);
    }
    
    public function testChangeExtension()
    {
        $model = Resource::make(__DIR__.'/../../src/file.txt')
            ->extension('html')
            ->mime('plain/html')
            ->save();
        
        $this->assertEquals('plain/html', $model->mime);
        $this->assertEquals('html', $model->extension);
        $this->assertEquals('file.html', $model->filename);
    }

    public function testChangeFilenameWithoutChanginTheExtension()
    {
        $model = Resource::make(__DIR__.'/../../src/file.txt')
            ->filename('renamed')
            ->save();
        
        $this->assertEquals('txt', $model->extension);
        $this->assertEquals('renamed.txt', $model->filename);
    }

    public function testChangeExtensionWithoutChangingTheFilename()
    {
        $model = Resource::make(__DIR__.'/../../src/file.txt')
            ->extension('html')
            ->save();
        
        $this->assertEquals('html', $model->extension);
        $this->assertEquals('file.html', $model->filename);
    }

    public function testParentAttribute()
    {
        $parent = Resource::make(__DIR__.'/../../src/file.txt')
            ->filename('parent.txt')
            ->save();
        
        $child = Resource::make(__DIR__.'/../../src/file.txt')
            ->filename('child.txt')
            ->parent($parent)
            ->save();        
        
        $this->assertNull($parent->parent);
        $this->assertEquals($child->parent, $parent);
        $this->assertCount(1, $parent->children);
    }

    public function testFormattedFilesizeAttribute()
    {
        $model = Resource::make(__DIR__.'/../../src/index.html')->save();
        
        $this->assertEquals('286 B', $model->size);
    }
}