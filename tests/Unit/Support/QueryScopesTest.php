<?php

namespace Tests\Unit\Support;

use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;
use Tests\TestCase;

class QueryScopesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        Resource::make(__DIR__.'/../../src/file.txt')
            ->disk('local')
            ->filename('a-1')
            ->context('a')
            ->caption('a-1')
            ->title('a-1')
            ->save();
        
        Resource::make(__DIR__.'/../../src/index.html')
            ->disk('public')
            ->filename('a-2')
            ->context('a')
            ->caption('a-2')
            ->title('a-2')
            ->save();

        Resource::make(__DIR__.'/../../src/file.txt')
            ->disk('local')
            ->filename('b-1')
            ->context('b')
            ->caption('b-1')
            ->title('b-1')
            ->save();

        Resource::make(__DIR__.'/../../src/index.html')
            ->disk('public')
            ->filename('b-2')
            ->context('b')
            ->caption('b-2')
            ->title('b-2')
            ->save();

        Resource::make(__DIR__.'/../../src/file.txt')
            ->disk('local')
            ->filename('c-1')
            ->context('c')
            ->caption('c-1')
            ->title('c-1')
            ->save();

        Resource::make(__DIR__.'/../../src/index.html')
            ->disk('public')
            ->filename('c-2')
            ->context('c')
            ->caption('c-2')
            ->title('c-2')
            ->save();
    }

    public function testScopeCaption()
    {
        $this->assertEquals(1, Media::caption('a-1')->count());
        $this->assertEquals(2, Media::caption('a-1', 'a-2')->count());
        $this->assertEquals(2, Media::caption(['a-1', 'a-2'])->count());
    }

    public function testScopeContext()
    {
        $this->assertEquals(2, Media::context('a')->count());
        $this->assertEquals(4, Media::context('a', 'b')->count());
        $this->assertEquals(4, Media::context(['a', 'b'])->count());
    }

    public function testScopeDisk()
    {
        $this->assertEquals(3, Media::disk('local')->count());
        $this->assertEquals(6, Media::disk('local', 'public')->count());
        $this->assertEquals(6, Media::disk(['local', 'public'])->count());
    }

    public function testScopeExtension()
    {
        $this->assertEquals(3, Media::extension('txt')->count());
        $this->assertEquals(6, Media::extension('txt', 'html')->count());
        $this->assertEquals(6, Media::extension(['txt', 'html'])->count());
    }

    public function testScopeFilename()
    {
        $this->assertEquals(1, Media::filename('a-1.txt')->count());
        $this->assertEquals(2, Media::filename('a-1.txt', 'a-2.html')->count());
        $this->assertEquals(2, Media::filename(['a-1.txt', 'a-2.html'])->count());
    }

    public function testScopeFilesize()
    {
        $this->assertEquals(3, Media::filesize(25)->count());
        $this->assertEquals(6, Media::filesize([25, 286])->count());
    }

    public function testScopeMime()
    {
        $this->assertEquals(3, Media::mime('text/plain')->count());
        $this->assertEquals(6, Media::mime(['text/plain', 'text/html'])->count());
    }

    public function testScopeTitle()
    {
        $this->assertEquals(1, Media::title('a-1')->count());
        $this->assertEquals(2, Media::title('a-1', 'a-2')->count());
        $this->assertEquals(2, Media::title(['a-1', 'a-2'])->count());
    }
}