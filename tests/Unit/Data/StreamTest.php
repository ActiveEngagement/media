<?php

namespace Tests\Unit\Data;

use Actengage\Media\Data\Stream;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StreamTest extends TestCase
{
    public function testSavingFileFromResource()
    {
        $file = Stream::make(fopen(__DIR__.'/../../src/file.txt', 'r+'));
        
        $this->assertEquals(25, $file->getSize());
        $this->assertEquals('txt', $file->extension());
        $this->assertEquals('file.txt', $file->filename());
        $this->assertEquals('text/plain', $file->mime());
        $this->assertEquals('This is some sample text.', $file->getContents());
    }

    public function testSavingFileFromPath()
    {
        $file = Stream::make(__DIR__.'/../../src/file.txt');
        
        $this->assertEquals(25, $file->getSize());
        $this->assertEquals('txt', $file->extension());
        $this->assertEquals('file.txt', $file->filename());
        $this->assertEquals('text/plain', $file->mime());
        $this->assertEquals('This is some sample text.', $file->getContents());
    }

    public function testSavingFileFromSplFileInfo()
    {
        $file = Stream::make(
            new UploadedFile(__DIR__.'/../../src/file.txt', 'file.text')
        );
        
        $this->assertEquals(25, $file->getSize());
        $this->assertEquals('txt', $file->extension());
        $this->assertEquals('file.txt', $file->filename());
        $this->assertEquals('text/plain', $file->mime());
        $this->assertEquals('This is some sample text.', $file->getContents());
    }

    public function testSavingFileFromString()
    {
        $file = Stream::make('This is some sample text.', [
            'metadata' => [
                'filename' => 'file.txt'
            ]
        ]);
        
        $this->assertEquals(25, $file->getSize());
        $this->assertEquals('txt', $file->extension());
        $this->assertEquals('file.txt', $file->filename());
        $this->assertEquals('text/plain', $file->mime());
        $this->assertEquals('This is some sample text.', $file->getContents());
    }
}