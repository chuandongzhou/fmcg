<?php

namespace App\Http\Controllers\ChildUser;


class CouponController extends Controller
{
    /**
     * CouponController constructor.
     */
    public function __construct()
    {
        $this->middleware('deposit:true');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = child_auth()->user()->shop;

        $coupons = $shop->coupons()->orderBy('end_at', 'desc')->get();

        return view('child-user.coupon.index', ['coupons' => $coupons]);
    }

}
