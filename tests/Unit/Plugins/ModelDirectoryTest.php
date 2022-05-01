<?php

namespace Tests\Unit\Plugins;

use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;
use Actengage\Media\Plugins\ModelDirectory;
use Actengage\Media\Resources\Image;
use Tests\TestCase;

class ModelDirectoryTest extends TestCase
{
    public function testModelDirectory()
    {
        Image::register([
            ModelDirectory::class
        ]);

        $model = Resource::path(__DIR__.'/../../src/image.jpeg')->save();
        
        $this->assertEquals(1, $model->directory);
    }

    public function testModelDirectoryUsingExtractor()
    {
        Image::register([
            [ModelDirectory::class, [
                'extractor' => PrimaryKeyExtractor::class
            ]]
        ]);

        $model = Resource::path(__DIR__.'/../../src/image.jpeg')->save();
        
        $this->assertEquals(1, $model->directory);
    }

    public function testModelDirectoryUsingExtractorInstance()
    {
        Image::register([
            [ModelDirectory::class, [
                'extractor' => new class {
                    public function __invoke(Media $model)
                    {
                        return $model->getKey();
                    }
                }
            ]]
        ]);

        $model = Resource::path(__DIR__.'/../../src/image.jpeg')->save();
        
        $this->assertEquals(1, $model->directory);
    }
}