<?php

namespace Tests\Unit\Plugins;

use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;
use Actengage\Media\Plugins\ExtractImageColors;
use Actengage\Media\Resources\Image;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExtractImageColorsTest extends TestCase
{
    public function testExtractImageColorsPlugin()
    {
        $file = new UploadedFile(
            __DIR__.'/../../src/image.jpeg', 'image.jpeg'
        );

        $model = Resource::make($file)->save();
        
        $this->assertCount(3, $model->colors);
        $this->assertEquals(
            ['#7491a9', '#202923', '#dee5e6'],
            $model->colors->map->getHex('#')->toArray()
        );
    }
}