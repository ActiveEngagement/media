<?php

namespace Tests\Unit\Plugins;

use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\PreserveOriginalResource;
use Tests\TestCase;

class PreserveOriginalResourceTest extends TestCase
{
    public function testPreservingOriginalResource()
    {
        Plugin::register([
            PreserveOriginalResource::class
        ]);

        $model = Resource::path(__DIR__.'/../../src/image.jpeg')
            ->disk('local')
            ->greyscale()
            ->save();

        $this->assertEquals(1, $model->children()->context('original')->count());
        $this->assertEquals('local', $model->children()->context('original')->first()->disk);
        $this->assertEquals(1, preg_match('/\w+_\w{8}.jpeg/', $model->children()->context('original')->first()->filename));


        $model = Resource::path(__DIR__.'/../../src/file.txt')
            ->disk('local')
            ->save();
        
        $this->assertEquals(1, $model->children()->context('original')->count());
        $this->assertEquals('local', $model->children()->context('original')->first()->disk);
        $this->assertEquals(1, preg_match('/\w+_\w{8}.txt/', $model->children()->context('original')->first()->filename));
    }
}