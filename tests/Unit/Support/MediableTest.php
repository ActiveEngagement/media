<?php

namespace Tests\Unit\Support;

use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tests\User;

class MediableTest extends TestCase
{
    protected Media $text;
    
    protected Media $html;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->text = Resource::make(__DIR__.'/src/file.txt')->save();
        $this->html = Resource::make(__DIR__.'/src/index.html')->save();
    }

    public function testMediable()
    {
        $user = User::create([
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'password' => Hash::make('password')
        ]);

        $user->media()->sync([
            $this->text->id => ['favorite' => true],
            $this->html->id => ['favorite' => false]
        ]);

        $this->assertCount(2, $user->media);
        $this->assertEquals(1, $user->media()->wherePivot('favorite', 1)->count());
        $this->assertInstanceOf(Media::class, $this->text);
        $this->assertEquals($this->text->id, $user->medium->id);

        $user->delete();

        $this->assertEquals(0, DB::table('mediables')->count());
    }
}