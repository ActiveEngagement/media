<?php

namespace Tests\Unit\Support;

use Actengage\Media\Support\ExifCoordinates;
use Actengage\Media\Support\ExifData;
use Intervention\Image\ImageManagerStatic;
use Tests\TestCase;

class ExifDataTest extends TestCase
{
    public function testExifData()
    {
        $img = ImageManagerStatic::make(
            __DIR__.'/../../src/image.jpeg'
        );

        $exif = new ExifData($img);

        $this->assertEquals('iPhone 7 Plus', $exif->model);
        $this->assertEquals('Apple', $exif->make);
        $this->assertInstanceOf(ExifCoordinates::class, $exif->coordinates());
        $this->assertEquals(38.98211388888889, $exif->latitude());
        $this->assertEquals(-104.9599, $exif->longitude());
        $this->assertNull($exif->thisKeyDoesntExist);
    }
}