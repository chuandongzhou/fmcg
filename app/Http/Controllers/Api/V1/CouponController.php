<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Coupon;
use Carbon\Carbon;
use Gate;


class CouponController extends Controller
{
    /**
     * 获取店铺可领优惠券
     *
     * @param $shop
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function coupon($shop)
    {
        $user = auth()->user();

        if (!$shop || $shop->user_type <= $user->type) {
            return $this->error('店铺不存在');
        }
        $coupons = $shop->coupons->filter(function ($coupon) {
            $coupon->shop->setAppends([]);
            return $coupon->can_receive;
        });

        return $this->success(['coupons' => $coupons->values()]);
        return $this->success(['coupons' => $coupons->values()]);
    }

    /**
     * 获取店铺可领优惠券数量
     *
     * @param $shop
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function couponNum($shop)
    {
        $user = auth()->user();
        $nowDate = (new Carbon)->toDateString();
        if (!$shop || $shop->user_type <= $user->type) {
            return $this->error('店铺不存在');
        }
        $couponNum = Coupon::whereNotIn('id', function ($query) use ($user) {
            $query->from('user_coupon')->where('user_id', $user->id)->select('coupon_id');
        })->where('stock', '>', 0)->where('end_at', '>', $nowDate)->where('shop_id', $shop->id)->count();
        return $this->success(['couponNum' => $couponNum]);
    }

    /**
     * 获取用户自己优惠券
     *
     * @param bool $expire
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function userCoupon($expire = false)
    {
        $now = (new Carbon())->toDateString();
        $user = auth()->user();
        $coupons = $user->coupons()->wherePivot('used_at', null)->with('shop')->where('end_at', '>=',
            $now)->orderBy('end_at',
            'DESC')->take($expire ? 5 : -1)->get()->each(function ($coupon) {
            $coupon->shop->setAppends([]);
        });

        return $this->success(['coupons' => $coupons]);
    }

    /**
     * 领取优惠券
     *
     * @param \App\Models\Coupon $coupon
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function receive(Coupon $coupon)
    {
        if (!$coupon->can_receive) {
            return $this->error('库存不足');
        }
        if (!$this->_changeStock($coupon)) {
            return $this->error('领取失败');
        }
        $user = auth()->user();

        $user->coupons()->attach($coupon->id, ['received_at' => Carbon::now()]);
        return $this->success(['coupon' => $coupon]);
    }

    /**
     * Display the specified resource.o
     *
     * @param \App\Models\Coupon $coupon
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function show(Coupon $coupon)
    {
        return $this->success(['coupon' => $coupon]);
    }


    private function _changeStock($coupon, $num = 1)
    {
        return $coupon->decrement('stock', $num);
    }
}
