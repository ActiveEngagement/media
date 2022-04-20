<?php

namespace Actengage\Media;

use Actengage\Media\Resources\Resource;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Config;
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

        Resource::setEventDispatcher(new Dispatcher());

        $this->app->singleton(ResourceManager::class, function() {
            return new ResourceManager();
        });

        $this->app->get(ResourceManager::class)->configure(
            Config::get('media.resources')
        );

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
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/media.php' => config_path('media.php')
        ], 'config');
    }
}