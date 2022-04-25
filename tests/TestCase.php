<?php

namespace Tests;

use Actengage\Media\Facades\Media;
use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\Resources\Image;
use Actengage\Media\Resources\Resource as BaseResource;
use Illuminate\Support\Facades\Storage;
use Actengage\Media\ServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
    * Setup the test environment.
    */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();

        $this->artisan('migrate', [
            '--database' => 'testbench'
        ]);

        Storage::fake('s3');
        Storage::fake('public');

        Plugin::flush();
        BaseResource::flushMacros();
        BaseResource::flushEventListeners();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Media' => Media::class,
            'Resource' => Resource::class
        ];
    }

}
