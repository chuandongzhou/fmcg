<?php

namespace WeiHeng\WechatPay;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\ServiceProvider;

class WechatPayServiceProvider extends ServiceProvider
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
        $this->app->singleton('wechat.pay', function ($app) {
            return new Sign($app['config']['wechat']);
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
            'wechat.pay',
        ];
    }

}
