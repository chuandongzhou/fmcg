<?php

namespace App\Http\Controllers\Index\Personal;

use App\Http\Controllers\Index\Controller;
use Illuminate\Http\Request;


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
        $shop = auth()->user()->shop;

        $coupons = $shop->coupons()->orderBy('end_at','desc')->get();

        return view('index.personal.coupon-index', ['coupons' => $coupons]);
    }

}
