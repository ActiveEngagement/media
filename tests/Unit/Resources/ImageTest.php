<?php

namespace Tests\Unit\Resources;

use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;
use Actengage\Media\Resources\Image;
use Actengage\Media\Support\ExifCoordinates;
use Actengage\Media\Support\ExifData;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImageTest extends TestCase
{
    public function testImageResource()
    {
        $file = new UploadedFile(
            __DIR__.'/../../src/image.jpeg', 'image.jpeg'
        );

        $resource = Resource::make($file)
            ->disk('public')
            ->directory('images')
            ->greyscale();

        $this->assertInstanceOf(Image::class, $resource);
        $this->assertInstanceOf(\Intervention\Image\Image::class, $resource->image());
        $this->assertEquals(2933093, $resource->filesize);
        $this->assertEquals('image/jpeg', $resource->mime);
        $this->assertEquals('jpeg', $resource->extension);
        $this->assertInstanceOf(ExifData::class, $resource->exif);
        $this->assertEquals(['999999', '272727', '444444'], $resource->palette(3)->map->getHex()->toArray());
        $this->assertEquals('a9a9a9', $resource->color()->getHex());
        
        $model = $resource->save();

        $this->assertInstanceOf(Media::class, $model);
        $this->assertTrue($model->file_exists);
        $this->assertEquals(2933093, $model->filesize);
        $this->assertEquals('jpeg', $model->extension);
        $this->assertEquals('images/image.jpeg', $model->relative_path);
        $this->assertEquals('/storage/images/image.jpeg', $model->url);
        $this->assertInstanceOf(ExifData::class, $model->exif);
        $this->assertInstanceOf(ExifCoordinates::class, $model->exif->coordinates());
    }
}