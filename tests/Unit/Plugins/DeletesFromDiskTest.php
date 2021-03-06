<?php

namespace Tests\Unit\Plugins;

use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\DeletesFromDisk;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeletesFromDiskTest extends TestCase
{
    public function testDeletableFromDiskTest()
    {
        Plugin::register([
            DeletesFromDisk::class
        ]);

        $file = new UploadedFile(
            __DIR__.'/../../src/image.jpeg', 'image.jpeg'
        );

        $model = Resource::make($file)
            ->directory('example')
            ->disk('public')
            ->save();

        Storage::disk('public')->assertExists('example/image.jpeg');

        $this->assertTrue($model->delete());
        Storage::disk('public')->assertMissing('example/image.jpeg');
    }
}