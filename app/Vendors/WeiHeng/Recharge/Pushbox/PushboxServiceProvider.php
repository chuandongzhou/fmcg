<?php

namespace WeiHeng\Recharge\Pushbox;

use Illuminate\Support\ServiceProvider;
use WeiHeng\Recharge\Pushbox\Adapter\Top;
use WeiHeng\Recharge\Pushbox\Sms;

class PushboxServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('pushbox.sms', function ($app) {
            return new Sms(new Top($app['config']['push.top']), $app['queue.connection']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['pushbox.sms'];
    }

}
