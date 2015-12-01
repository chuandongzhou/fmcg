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

        /**
         * 要显示的商品
         */
        $gate->define('validate-goods', function ($user, $goods) {
            return $user->type < $goods->user_type && $goods->status == cons('status.on');
        });

        /**
         * 我的商品
         */
        $gate->define('validate-my-goods', function ($user, $goods) {
            return $user->type == $goods->user_type;
        });

        /**
         * 我的商店
         */
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
         * 验证在线付款订单
         */
        $gate->define('validate-online-orders', function ($user, $orders) {
            if ($orders instanceof Order) {
                return $orders->can_payment;
            } else if ($orders instanceof Collection) {
                foreach ($orders as $order) {
                    if (!$order->can_payment) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        });

        /**
         * 验证是否可退款
         */
        $gate->define('validate-refund-order', function ($user, $order) {
            return $order->pay_type == cons('pay_type.online') && $order->pay_status == cons('trade.pay_status.success') && $order->status == cons('order.status.non_send') && $order->user_id == $user->id;
        });

    }
}