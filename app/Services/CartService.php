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

    protected $data = [];

    public function __construct($data = [])
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 格式化购物车商品
     *
     * @param $carts
     */
    public function formatCarts($carts = null)
    {
        $carts = is_null($carts) ? $this->data : $carts;
        $shopIds = $carts->pluck('goods.shop_id');
        $shopIds = $shopIds->all();

        $shops = Shop::whereIn('id', $shopIds)->select(['name', 'id', 'min_money', 'user_id'])->get();

        foreach ($shops as $key => $shop) {
            $sumPrice = 0;
            foreach ($carts as $cart) {
                if (Gate::denies('validate-goods', $cart->goods)) {
                    continue;
                }
                $cart->is_like = !is_null( auth()->user()->likeGoods()->where('id' , $cart->goods_id)->first());
                $cart->image = $cart->goods->image_url;
                if ($cart->goods->shop_id == $shop->id) {

                    $shops[$key]->cart_goods = $shops[$key]->cart_goods ? array_merge($shops[$key]->cart_goods,
                        [$cart]) : [$cart];
                    $sumPrice += $cart->goods->price * $cart->num;
                }
            }
            $sumPrice > 0 ? $shops[$key]->sum_price = $sumPrice : array_except($shops ,$key);
        }
        return $shops;
    }

    /**
     * 购买商品数据验证
     *
     * @param $num
     * @param bool|false $updateNum
     * @return bool
     */
    public function validateOrder($num , $updateNum = false)
    {
        $carts = $this->data;
        if (empty($carts[0]) || empty($num)) {
            return false;
        }
        //是否通过验证
        $allow = true;
        //判断商品购买数量是否小于该商品的最低配送额
        foreach ($carts as $cart) {
            $buyNum = $num[$cart->goods_id];
            if ($cart->goods->min_num > $buyNum) {
                $allow = false;
            }
            $updateNum && $cart->fill(['num' => $buyNum])->save();
        }
        if (!$allow) {
            return false;
        }

        $shops = $this->formatCarts();
        // 判断购买金额是否小于商店的最低配送额
        foreach ($shops as $shop) {
            if ($shop->min_money > $shop->sum_price) {
                return false;
            }
        }
        return $shops;
    }

}