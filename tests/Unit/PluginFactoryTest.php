<?php

namespace Tests\Unit;

use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Plugins\ExtractImageColors;
use Actengage\Media\Plugins\HashDirectory;
use Actengage\Media\Plugins\HashFilename;
use Actengage\Media\Plugins\PreserveOriginalResource;
use Tests\TestCase;

class PluginFactoryTest extends TestCase
{
    public function testMergingAndRemovingConfigurations()
    {
        Plugin::register([
            PreserveOriginalResource::class,
            [HashDirectory::class, [
                'length' => 8
            ]],
            'image' => [
                [ExtractImageColors::class, [
                    'colorCount' => 3
                ]],
                [HashFilename::class, [
                    'length' => 8
                ]]
            ],
            'file' => [
                [HashFilename::class, [
                    'length' => 8
                ]]
            ]
        ]);

        $this->assertCount(4, Resource::make(__DIR__.'/../src/image.jpeg')->plugins());
        $this->assertCount(3, Resource::make(__DIR__.'/../src/file.txt')->plugins());

        Plugin::unregister([
            [HashDirectory::class, [
                'length' => 8
            ]],
            'file' => [
                [HashFilename::class, [
                    'length' => 8
                ]]
            ],
            'image' => [
                [HashFilename::class, [
                    'length' => 8
                ]]
            ]
        ]);

        $this->assertCount(2, Resource::make(__DIR__.'/../src/image.jpeg')->plugins());
        $this->assertCount(1, Resource::make(__DIR__.'/../src/file.txt')->plugins());
    }

    public function testMergingGroupConfiguration()
    {
        $this->assertCount(0, Plugin::config());

        Plugin::registerGroup('image', [
            [HashFilename::class, [
                'length' => 8
            ]]
        ]);

        Plugin::registerGroup('file', [
            [HashFilename::class, [
                'length' => 8
            ]]
        ]);

        $this->assertCount(2, Plugin::config());
    }

    public function testRemovingGroupConfigurations()
    {
        Plugin::register([
            [HashDirectory::class, [
                'length' => 8
            ]],
            'image' => [
                [HashFilename::class, [
                    'length' => 8
                ]]
            ],
            'file' => [
                [HashFilename::class, [
                    'length' => 8
                ]]
            ]
        ]);

        $this->assertCount(3, Plugin::config());

        Plugin::unregisterGroup(['image', 'file']);

        $this->assertCount(1, Plugin::config());
    }
}