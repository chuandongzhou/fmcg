<?php

namespace WeiHeng\UnionPay;

use Illuminate\Support\ServiceProvider;

class UnionPayServiceProvider extends ServiceProvider
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
        $this->app->singleton('union.pay', function ($app) {
            return new UnionPay($app['config']['union-pay']);
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
            'union.pay',
        ];
    }

}
