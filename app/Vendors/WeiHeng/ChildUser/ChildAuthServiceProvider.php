<?php

namespace WeiHeng\ChildUser;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\ServiceProvider;

class ChildAuthServiceProvider extends ServiceProvider
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
        $this->app->bind('child.auth', function ($app) {
            $provider = new EloquentUserProvider($app['hash'], \App\Models\ChildUser::class);
            $guard = new Guard($provider, $app['session.store']);

            // 设置变量
            if (method_exists($guard, 'setCookieJar')) {
                $guard->setCookieJar($app['cookie']);
            }

            if (method_exists($guard, 'setDispatcher')) {
                $guard->setDispatcher($app['events']);
            }

            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($app->refresh('request', $guard, 'setRequest'));
            }

            return $guard;
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
            'child.auth',
        ];
    }

}
