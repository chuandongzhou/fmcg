<?php

namespace App\Providers;

use App\Models\Order;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Database\Eloquent\Collection;
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

        $gate->define('validate-my-goods', function ($user, $goods) {
            return $user->type == $goods->user_type;
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

        /**
         * 验证订单
         */
        $gate->define('validate-online-orders', function ($user, $orders) {
            if ($orders instanceof Order) {
                if ($orders->pay_type != cons('pay_type.online')) {
                    return false;
                }
                $orderConfig = cons('order');

                if ($orders->pay_status != $orderConfig['pay_status']['non_payment']) {
                    return false;
                }

                return $user->id == $orders->user_id;
            } else if ($orders instanceof Collection) {
                foreach ($orders as $order) {
                    if ($user->id != $order->user_id) {
                        return false;
                    }
                    if ($order->pay_type != cons('pay_type.online')) {
                        return false;
                    }
                    $orderConfig = cons('order');

                    if ($order->pay_status != $orderConfig['pay_status']['non_payment']) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        });
    }
}