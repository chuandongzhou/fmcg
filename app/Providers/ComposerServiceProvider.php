<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\CategoryComposer;
use App\Http\ViewComposers\UserComposer;
use App\Http\ViewComposers\ProvinceComposer;

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
                    'index.like.goods'
                ],
                UserComposer::class => [
                    'master'
                ],
                ProvinceComposer::class => [
                    'index.index-top'
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
