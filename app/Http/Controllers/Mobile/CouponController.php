<?php

namespace App\Http\Controllers\Mobile;


use Carbon\Carbon;

class CouponController extends Controller
{
    public function index()
    {
        $now = (new Carbon())->toDateString();
        $user = auth()->user();
        $coupons = $user->coupons()->wherePivot('used_at', null)->with('shop')->where('end_at', '>=',
            $now)->orderBy('end_at', 'DESC')->get()->each(function ($coupon) {
            $coupon->shop->setAppends([]);
        });

        return view('mobile.coupon.index', compact('coupons'));
    }

}
