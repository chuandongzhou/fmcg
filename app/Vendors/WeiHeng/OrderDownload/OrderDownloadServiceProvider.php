<?php

namespace WeiHeng\OrderDownload;

use Illuminate\Support\ServiceProvider;

class OrderDownloadServiceProvider extends ServiceProvider
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
        $this->app->singleton('order.download', function ($app) {
            return new OrderDownload($app['redis']);
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
            'order.download',
        ];
    }

}
