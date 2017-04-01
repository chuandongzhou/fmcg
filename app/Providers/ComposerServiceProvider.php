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
                'index.index-master',
                'includes.shop-search',
                'errors.404'
            ],
            UserComposer::class => [
                'master',
                'index.master',
                'index.manage-master',
                'index.index.index',
                'includes.search',
                'includes.shop-search',
                'index.menu-master',
                'index.personal.tabs',
                'index.help.master',
                'index.shop.detail',
                'includes.goods-list',
                'index.goods.detail',
                'includes.quick-link',
                'index.personal.security.security-index',
                'index.help.master',
                'errors.404'
            ],
            ProvinceComposer::class => [
                'index.master'
            ],
            NodeComposer::class => [
                'admin.master'
            ],
            CartComposer::class => [
                'includes.search',
                'includes.shop-search',
                'index.master',
                'includes.navigator',
                'index.manage-master'
            ],
            KeywordsComposer::class => [
                'includes.search',
                'includes.shop-search',
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
