<?php

namespace App\Providers;

use App\Http\ViewComposers\ChildNodeComposer;
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
                'mobile.category.index',
                'mobile.index.index',
                'errors.404'
            ],
            UserComposer::class => [
                'master',
                'index.master',
                'index.manage-master',
                'index.index.index',
                'includes.search',
                'includes.menu',
                'includes.shop-search',
                'index.menu-master',
                'index.personal.tabs',
                'index.help.master',
                'index.shop.detail',
                'includes.goods-list',
                'includes.quick-link',
                'index.personal.security.security-index',
                'index.help.master',
                'errors.404'
            ],
            ProvinceComposer::class => [
                'index.master',
                'mobile.index.index'
            ],
            NodeComposer::class => [
                'admin.master'
            ],
            ChildNodeComposer::class => [
                'includes.child-menu'
            ],
            CartComposer::class => [
                'includes.search',
                'includes.shop-search',
                'index.master',
                'includes.navigator',
                'index.manage-master',
                'mobile.includes.footer',
                'mobile.goods.detail',
                'mobile.cart.index'
            ],
            KeywordsComposer::class => [
                'includes.search',
                'includes.shop-search',
                'index.master',
                'mobile.search.index',
                'mobile.search.shop-goods'
            ],
            ChatComposer::class => [
                'includes.chat',
                'index.personal.chat-kit',
                'child-user.chat.kit'
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
