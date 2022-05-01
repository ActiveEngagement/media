<?php

namespace Tests\Unit\Plugins;

use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\UpdateFilesizeAfterStoring;
use Tests\TestCase;

class UpdateFilesizeAfterStoringTest extends TestCase
{
    public function testUpdatingFilesizeAfterStoring()
    {
        Plugin::register([
            UpdateFilesizeAfterStoring::class
        ]);

        $resource = Resource::path(__DIR__.'/../../src/image.jpeg')->resize(10, 10);
        
        $this->assertEquals(2933093, $resource->filesize);

        $model = $resource->save();

        $this->assertEquals(835, $resource->filesize);
        $this->assertEquals(835, $model->filesize);
    }
}