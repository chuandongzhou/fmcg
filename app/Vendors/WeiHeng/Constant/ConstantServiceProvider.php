<?php

namespace WeiHeng\Constant;

use Illuminate\Support\ServiceProvider;

class ConstantServiceProvider extends ServiceProvider
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
        $this->app->singleton('constant', function ($app) {
            return new Constant($app['config'], $app['translator']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['constant'];
    }

}
