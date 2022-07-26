<?php

namespace Tests\Unit\Resources;

use Actengage\Media\Data\Stream;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;
use Actengage\Media\Resources\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileTest extends TestCase
{
    public function testFileResource()
    {
        $file = new UploadedFile(
            __DIR__.'/../../src/file.txt', 'file.txt'
        );

        $resource = Resource::make($file)
            ->disk('public')
            ->directory('files');

        $this->assertInstanceOf(File::class, $resource);
        $this->assertInstanceOf(Stream::class, $resource->stream());
        $this->assertEquals('txt', $resource->extension);
        $this->assertEquals('text/plain', $resource->mime);
        
        $model = $resource->save();

        $this->assertInstanceOf(Media::class, $model);
        $this->assertTrue($model->file_exists);
        $this->assertEquals(25, $model->filesize);
        $this->assertEquals('txt', $model->extension);
        $this->assertEquals('files/file.txt', $model->relative_path);
        $this->assertEquals('/storage/files/file.txt', $model->url);

        // Ensure that by default files on disk are not deleted when the Media record is. That behavior is reserved for
        // the DeletesFromDisk plugin.

        $this->assertTrue($model->delete());
        Storage::disk('public')->assertExists('files/file.txt');
    }
}