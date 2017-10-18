<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\SalesmanVisitOrder;
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
            /*$goodsShopUser = $goods->shop->user;
            $nowTime = Carbon::now();*/
            $userTypes = cons('user.type');
            if ($user->type == $userTypes['supplier']) {
                $allow = $goods->user_type == $userTypes['maker'];
            } else {
                $allow = $goods->user_type > $user->type && $goods->user_type < $userTypes['maker'];
            }

            return $user->id == $goods->shop->user_id || ($allow && $goods->status == cons('status.on')/* && $goodsShopUser->deposit > 0 && $goodsShopUser->expire_at > $nowTime*/);
        });

        /**
         * 我的商品
         */
        $gate->define('validate-my-goods', function ($user, $goods) {
            return $user->shop_id == $goods->shop_id;
        });

        /**
         * 我的商店
         */
        $gate->define('validate-shop', function ($user, $shop) {
            return $user->shop_id == $shop->id;
        });

        /**
         * 验证是否有权限访问店铺
         */
        $gate->define('validate-allow', function ($user, $shop) {
            //$shopUser = $shop->user;
            //$nowTime = Carbon::now();
            //return ($user->type < $shop->user_type && $shopUser->deposit > 0 && $shopUser->expire_at > $nowTime) || $user->id == $shop->user_id;
            $userTypes = cons('user.type');
            if ($user->type == $userTypes['supplier']) {
                $allow = $shop->user_type == $userTypes['maker'];
            } else {
                $allow = $shop->user_type > $user->type && $shop->user_type < $userTypes['maker'];
            }
            return $user->id == $shop->user_id || $allow;
        });

        /**
         * 验证在线付款订单
         */
        $gate->define('validate-payment-orders', function ($user, $orders) {
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
         * 判断业务员权限
         */
        $gate->define('validate-salesman', function ($user, $salesman) {
            return $user->shop_id == $salesman->shop_id || $salesman->maker_id;
        });

        /**
         * 判断客户权限
         */
        $gate->define('validate-customer', function ($user, $customer) {
            return ($user->shop_id == ($customer->salesman->shop_id ?? '') || $user->shop_id == ($customer->salesman->maker_id ?? '') || $customer->belong_shop == $user->shop_id);
        });

        /**
         * 判断业务订单权限
         */
        $gate->define('validate-salesman-order', function ($user, $order) {
            $shopSalesmenIds = $user->shop->salesmen->pluck('id')->toBase();
            if ($order instanceof SalesmanVisitOrder) {
                return $shopSalesmenIds->contains($order->salesman_id);
            } else {
                if ($order->isEmpty()) {
                    return false;
                }
                foreach ($order as $item) {
                    if (!$shopSalesmenIds->contains($item->salesman_id)) {
                        return false;
                    }
                }
                return true;
            }
        });

        /**
         * 验证抵费商品
         */
        $gate->define('validate-mortgage-goods', function ($user, $mortgageGoods) {
            return $mortgageGoods->shop->id == $user->shop_id;
        });

        /**
         * 验证店铺代金券
         */
        $gate->define('validate-shop-coupon', function ($user, $coupon) {
            return $coupon->shop_id == $user->shop_id;
        });

        //验证子账号
        $gate->define('child-user', function ($user, $childUser) {
            return $childUser->user_id == $user->id;
        });
        /**
         * 验证店铺资产
         */
        $gate->define('validate-shop-asset', function ($user, $asset) {
            return $asset->shop_id == $user->shop_id;
        });

        /**
         * 验证店铺资产审核权限
         */
        $gate->define('validate-shop-assetApply', function ($user, $asset_apply) {
            return $asset_apply->asset->shop_id == $user->shop_id;
        });

        /**
         * 验证店铺促销活动
         */
        $gate->define('validate-shop-promo', function ($user, $promo) {
            return $user->shop_id == $promo->shop_id;
        });

        /**
         * 验证店铺促销审核权限
         */
        $gate->define('validate-shop-promoApply', function ($user, $promoApply) {
            return $user->shop_id == $promoApply->promo->shop_id;
        });

        /**
         * 验证业务员资产申请删除权限
         */
        $gate->define('validate-salesman-assetApply', function ($salesman, $assetApply) {
            return $salesman->id == $assetApply->salesman_id;
        });

        /**
         * 验证业务员促销申请删除权限
         */
        $gate->define('validate-salesman-promoApply', function ($salesman, $promoApply) {
            return $salesman->id == $promoApply->salesman_id;
        });
    }
}