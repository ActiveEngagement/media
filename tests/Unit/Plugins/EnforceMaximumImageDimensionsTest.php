<?php

namespace Tests\Unit\Plugins;

use Actengage\Media\Exceptions\BadAttributeException;
use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\EnforceMaximumImageDimensions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class EnforceMaximumImageDimensionsTest extends TestCase
{
    public function testExtractImageColorsPlugin()
    {
        Plugin::register([
            [EnforceMaximumImageDimensions::class, [
                'width' => 100,
                'height' => 100
            ]]
        ]);

        $file = new UploadedFile(
            __DIR__.'/../../src/image.jpeg', 'image.jpeg'
        );

        $resource = Resource::make($file);

        $this->assertEquals(100, $resource->image()->width());
        $this->assertEquals(75, $resource->image()->height());
    }

    public function testRegisteringPluginToFile()
    {
        Plugin::register([
            EnforceMaximumImageDimensions::class
        ]);

        $file = new UploadedFile(
            __DIR__.'/../../src/file.txt', 'file.txt'
        );

        try {
            Resource::make($file)->save();
        }
        catch(BadAttributeException $e) {
            $this->addToAssertionCount(1);
        }

        $this->expectNotToPerformAssertions();
    }
}