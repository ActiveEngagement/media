<?php

namespace Actengage\Media;

use Actengage\Media\Resources\Resource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/media.php', 'media'
        );

        $this->app->singleton(PluginFactory::class, function($app) {
            return new PluginFactory($app);
        });

        $this->app->singleton(ResourceFactory::class, function($app) {
            return new ResourceFactory($app);
        });
        
        $this->app->bind(Media::class, function($app, $args) {
            $class = Config::get('media.model');

            return new $class($args);
        });
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Resource::setEventDispatcher($this->app['events']);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/media.php' => config_path('media.php')
        ], 'media-config');
        
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations')
        ], 'media-migrations');
        
        $this->publishes([
            __DIR__.'/../database/legacy' => database_path('migrations')
        ], 'media-migrations-legacy');

        $this->app->get(PluginFactory::class)->boot();
    }

    protected function bootPlugins($factory)
    {
        dd(Config::get('media.plugins', []));

        foreach(Config::get('media.plugins', []) as $key => $plugins) {
            if($resource = $factory->resource($key)) {
                $resource::register($plugins);
            }
        }
    }
}