<?php

namespace WeiHeng\ULinePay;

use Illuminate\Support\ServiceProvider;

class ULinePayServiceProvider extends ServiceProvider
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
        $this->app->singleton('uline.pay', function ($app) {
            return new ULinePay($app['config']['uline-pay']);
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
            'uline.pay',
        ];
    }

}
