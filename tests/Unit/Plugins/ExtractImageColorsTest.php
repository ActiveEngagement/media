<?php

namespace Tests\Unit\Plugins;

use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\ExtractImageColors;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExtractImageColorsTest extends TestCase
{
    public function testExtractImageColorsPlugin()
    {
        Plugin::register([
            [ExtractImageColors::class, [
                'colorCount' => 3,
                'quality' => 10
            ]]
        ]);

        $file = new UploadedFile(
            __DIR__.'/../../src/image.jpeg', 'image.jpeg'
        );

        $model = Resource::make($file)->save();
        
        $this->assertCount(3, $model->colors);
    }
}