<?php

namespace WeiHeng\ChildUser;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\ServiceProvider;

class ChildUserServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('child.user', function ($app) {
            return new ChildUser($app['cache.store']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'child.user'
        ];
    }

}
