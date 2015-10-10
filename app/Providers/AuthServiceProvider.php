<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        parent::registerPolicies($gate);

        /**
         * 验证用户
         */
        $gate->define('validate-user', function ($user, $post) {
            return $user->id === $post->user_id;
        });

        $gate->define('validate-goods', function ($user, $goods) {
            return $user->type < $goods->user_type && $goods->status == cons('status.on');
        });

        $gate->define('validate-shop', function ($user, $shop) {
            return $user->shop->id === $shop->id;
        });
        /**
         * 验证是否有权限访问店铺
         */
        $gate->define('validate-allow', function ($user, $shop) {
            return $user->type <= $shop->user->type;
        });
    }
}