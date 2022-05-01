<?php

namespace Tests\Unit\Plugins;

use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\ExtractImageMetaData;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExtractImageMetaDataTest extends TestCase
{
    public function testExtractImageMetaData()
    {
        Plugin::register([
            ExtractImageMetaData::class
        ]);

        $file = new UploadedFile(
            __DIR__.'/../../src/image.jpeg', 'image.jpeg'
        );

        $model = Resource::make($file)
            ->resize(10, 10)
            ->save();
        
        $this->assertEquals(10, $model->meta->get('width'));
        $this->assertEquals(10, $model->meta->get('height'));
    }
}