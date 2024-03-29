<?php

namespace Tests\Unit\Resources;

use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;
use Actengage\Media\Resources\Image;
use Actengage\Media\Support\ExifCoordinates;
use Actengage\Media\Support\ExifData;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Unit\Support\DummyFilesystem;

class ImageTest extends TestCase
{
    public function testImageResource()
    {
        $file = new UploadedFile(
            __DIR__.'/../../src/image.jpeg', 'image.jpeg'
        );

        $resource = Resource::make($file)
            ->disk('public')
            ->directory('images');

        $this->assertInstanceOf(Image::class, $resource);
        $this->assertInstanceOf(\Intervention\Image\Image::class, $resource->image());
        $this->assertEquals(2933093, $resource->filesize);
        $this->assertEquals('image/jpeg', $resource->mime);
        $this->assertEquals('jpeg', $resource->extension);
        $this->assertInstanceOf(ExifData::class, $resource->exif);
        
        $model = $resource->save();

        $this->assertInstanceOf(Media::class, $model);
        $this->assertTrue($model->file_exists);
        $this->assertEquals(2933093, $model->filesize);
        $this->assertEquals('jpeg', $model->extension);
        $this->assertEquals('images/image.jpeg', $model->relative_path);
        $this->assertEquals('/storage/images/image.jpeg', $model->url);
        $this->assertInstanceOf(ExifData::class, $model->exif);
        $this->assertInstanceOf(ExifCoordinates::class, $model->exif->coordinates());

        // Ensure that by default files on disk are not deleted when the Media record is. That behavior is reserved for
        // the DeletesFromDisk plugin.

        $this->assertTrue($model->delete());
        Storage::disk('public')->assertExists('images/image.jpeg');
    }

    public function testSavePassesStorageOptions()
    {
        $fs = new DummyFilesystem;
        Storage::shouldReceive('disk')->andReturn($fs);
        
        $file = new UploadedFile(
            __DIR__.'/../../src/image.jpeg', 'image.jpeg'
        );

        Resource::make($file)
            ->disk('public')
            ->directory('images')
            ->storageOptions([
                'example' => true,
                'config' => 'two'
            ])
            ->save();
        
        $this->assertEquals(
            [
                'example' => true,
                'config' => 'two'
            ],
            $fs->options
        );
    }
}