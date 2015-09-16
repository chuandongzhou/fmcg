<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\CategoryComposer;
use App\Http\ViewComposers\UserComposer;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composers([
                CategoryComposer::class => [
                    'index.master',
                    'index.index.index',
                    'index.index-master',
                    'index.retailer-master'
                ],
                UserComposer::class => [
                    'master'
                ]
            ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
