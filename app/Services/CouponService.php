<?php

namespace App\Services;

use App\Models\Coupon;
use Cache;
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class CouponService
{
    public static function shopCouponNum($shop_id)
    {
        $user = auth()->user();
        $nowDate = (new Carbon)->toDateString();
        $couponNum = Coupon::whereNotIn('id', function ($query) use ($user) {
            $query->from('user_coupon')->where('user_id', $user->id)->select('coupon_id');
        })->where('shop_id', $shop_id)->where('stock', '>', 0)->where('end_at', '>', $nowDate)->count();
        return $couponNum;

    }
}
