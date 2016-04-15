<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\CategoryComposer;
use App\Http\ViewComposers\UserComposer;
use App\Http\ViewComposers\ProvinceComposer;
use App\Http\ViewComposers\NodeComposer;
use App\Http\ViewComposers\CartComposer;
use App\Http\ViewComposers\KeywordsComposer;
use App\Http\ViewComposers\ChatComposer;

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
                'index.index.index',
                'index.index-master'
            ],
            UserComposer::class => [
                'master',
                'index.master',
                'index.manage-master',
                'index.index.index',
                'index.search',
                'index.shop-search',
                'index.menu-master',
                'index.personal.tabs',
                'index.help.master'
            ],
            ProvinceComposer::class => [
               // 'index.search',
                'index.master'
            ],
            NodeComposer::class => [
                'admin.master'
            ],
            CartComposer::class => [
                'index.search',
                'index.shop-search',
                'index.master'
            ],
            KeywordsComposer::class => [
                'index.search',
                'index.shop-search',
                'index.master'
            ],
            ChatComposer::class => [
                'includes.chat',
                'index.personal.chat-kit'
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
