<?php

namespace App\Services;

use App\Models\Shop;
use Gate;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class CartService
{

    protected $carts = [];

    public function __construct($carts = [])
    {
        $this->carts = $carts;
        return $this;
    }

    /**
     * 格式化购物车商品
     *
     * @param $carts
     */
    public function formatCarts($carts = null)
    {
        $carts = is_null($carts) ? $this->carts : $carts;
        $shopIds = $carts->pluck('goods.shop_id');
        $shopIds = $shopIds->all();

        $shops = Shop::whereIn('id', $shopIds)->select(['name', 'id', 'min_money'])->get();

        foreach ($shops as $key => $shop) {
            $sumPrice = 0;
            foreach ($carts as $cart) {
                if (Gate::denies('validate-goods', $cart->goods)) {
                    continue;
                }
                if ($cart->goods->shop_id == $shop->id) {
                    $shops[$key]->cart_goods = $shops[$key]->cart_goods ? array_merge($shops[$key]->cart_goods, [$cart]) : [$cart];
                    $sumPrice +=   $cart->goods->price * $cart->num;
                }
            }
            $shops[$key]->sum_price = $sumPrice;
        }
        return $shops;
    }

}