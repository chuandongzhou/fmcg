<?php

namespace WeiHeng\SignManage;

use Illuminate\Support\ServiceProvider;

class SignManageServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    //protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sign', function ($app) {
            return new Sign($app['config']['constant.sign']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     *
     */
    public function provides()
    {
        return [
            'sign',
        ];
    }

}
