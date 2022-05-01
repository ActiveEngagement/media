<?php

namespace Tests;

use Actengage\Media\Facades\Media;
use Actengage\Media\Facades\Plugin;
use Actengage\Media\Facades\Resource;
use Actengage\Media\PluginFactory;
use Actengage\Media\ResourceFactory;
use Illuminate\Support\Facades\Storage;
use Actengage\Media\ServiceProvider;
use Illuminate\Support\Facades\Event;
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

        foreach(config('media.resources') as $resource) {
            $resource::flushMacros();
            $resource::flushEventListeners();
        }

        $this->app->get(ResourceFactory::class)->boot();
        $this->app->get(PluginFactory::class)->boot();
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
