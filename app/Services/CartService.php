<?php

namespace App\Services;

use App\Models\Shop;
use Gate;
use Cache;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class CartService
{

    protected $data = [], $cacheKey;

    public function __construct($data = [])
    {
        $this->data = $data;
        $this->cacheKey = 'cart:' . auth()->id();
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
        $shopIds = array_unique($shopIds->all());

        $shops = Shop::whereIn('id', $shopIds)->select([
            'name',
            'id',
            'min_money',
            'user_id'
        ])->with('user')->get()->each(function ($shop) {
            $shop->setAppends([]);
        });
        $userLikeGoodsIds = auth()->user()->likeGoods()->get()->pluck('id')->all();
        foreach ($shops as $key => $shop) {
            $sumPrice = 0;
            foreach ($carts as $cart) {
                if (Gate::denies('validate-goods', $cart->goods)) {
                    continue;
                }
                $cart->is_like = in_array($cart->goods_id, $userLikeGoodsIds);
                $cart->image = $cart->goods->image_url;
                if ($cart->goods->shop_id == $shop->id) {
                    $shops[$key]->cart_goods = $shops[$key]->cart_goods ? array_merge($shops[$key]->cart_goods,
                        [$cart]) : [$cart];
                    $sumPrice += $cart->goods->price * $cart->num;
                }
            }
            $sumPrice > 0 ? $shops[$key]->sum_price = $sumPrice : array_except($shops, $key);
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
    public function validateOrder($num, $updateNum = false)
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
            if ($cart->goods->min_num > $buyNum || $cart->goods->is_out || $buyNum > 10000) {
                $allow = false;
            }
            $updateNum && $cart->fill(['num' => $buyNum])->save();
        }
        if (!$allow) {
            return false;
        }

        $shops = $this->formatCarts();
        return $shops;
    }

    /**
     * 是否存在key
     *
     * @param null $key
     * @return mixed
     */
    public function has($key = null)
    {
        $key = is_null($key) ? $this->cacheKey : $key;
        return Cache::has($key);
    }

    /**
     * 设置购物车数量
     *
     * @param $count
     */
    public function set($count)
    {
        return Cache::forever($this->cacheKey, $count);
    }

    /**
     * 获取购物车数量
     *
     * @param null $key
     * @return mixed
     */
    public function get($key = null)
    {
        $key = is_null($key) ? $this->cacheKey : $key;
        return Cache::get($key);
    }

    /**
     * 增加购物车数量
     *
     * @param int $count
     */
    public function increment($count = 1)
    {
        return $this->has($this->cacheKey) ? Cache::increment($this->cacheKey, $count) : $this->set($count);
    }

    /**
     * 减少购物车数量
     *
     * @param $count
     * @return string
     */
    public function decrement($count = 1)
    {
        return $this->has($this->cacheKey) ? Cache::decrement($this->cacheKey, $count) : '';
    }

}