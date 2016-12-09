<?php

namespace App\Services;

use App\Models\Shop;
use Carbon\Carbon;
use Gate;
use Cache;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class CartService extends BaseService
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
     * @param null $carts
     * @param bool $withCoupon
     * @param bool $isDelivery
     * @return array
     */
    public function formatCarts($carts = null, $withCoupon = false, $isDelivery = true)
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
                    $cart->delete();
                    continue;
                }
                $cartGoods = $cart->goods;

                $cart->is_like = in_array($cart->goods_id, $userLikeGoodsIds);
                $cart->image = $cartGoods->image_url;
                if ($cartGoods->shop_id == $shop->id) {
                    $shop->cart_goods = $shop->cart_goods ? $shop->cart_goods->push($cart) : collect([$cart]);
                    $cartGoodsPrice = $isDelivery ? $cartGoods->price : $cartGoods->pick_up_price;
                    $sumPrice += $cartGoodsPrice * $cart->num;
                }
            }
            if ($sumPrice > 0) {
                $shop->sum_price = $sumPrice;
                $withCoupon && ($shop->coupons = $this->getUsefulCoupon($shop->id, $sumPrice));

            } else {
                $shops = array_except($shops, $key);
            }
        }
        return $shops;
    }

    /**
     * 获取可用优惠券
     *
     * @param $shopId
     * @param $sumPrice
     * @return mixed
     */
    public function getUsefulCoupon($shopId, $sumPrice)
    {
        return auth()->user()->coupons()->wherePivot('used_at', null)->OfUseful($shopId, $sumPrice)->orderBy('discount',
            'DESC')->get();
    }

    /**
     * 购买商品数据验证
     *
     * @param $num
     * @param bool $updateNum
     * @param bool $isDelivery
     * @return array|bool
     */
    public function validateOrder($num, $updateNum = false, $isDelivery = true)
    {
        $carts = $this->data;
        if ($carts->isEmpty() || empty($num)) {
            $this->setError('购物车为空');
            return false;
        }
        //是否通过验证
        $allow = true;

        foreach ($carts as $cart) {
            $buyNum = $num[$cart->goods_id];
            //判断商品购买数量是否小于该商品的最低配送额
            /*$cart->goods->min_num > $buyNum || */
            if ($cart->goods->is_out || $buyNum > 20000) {
                $allow = false;
            }
            $updateNum && $cart->fill(['num' => $buyNum])->save();
        }
        if (!$allow) {
            $this->setError('商品缺货或数量大于两万');
            return false;
        }

        $shops = $this->formatCarts(null, false, $isDelivery);
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

    /**
     * 获取购物车数量
     *
     */
    public function cartDetail()
    {
        $user = auth()->user();
        $myCarts = $user->carts();
        $carts['detail'] = $myCarts->whereHas('goods', function ($query) use ($user) {
            $query->whereNotNull('id')->where('user_type', '>', $user->type);
        })->with('goods.images.image')->get();
        $carts['count'] = count($carts['detail']);

        return $carts;
    }
}