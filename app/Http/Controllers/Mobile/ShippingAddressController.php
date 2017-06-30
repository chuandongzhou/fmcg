<?php

namespace App\Http\Controllers\Mobile;

use App\Models\ShippingAddress;

class ShippingAddressController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $shippingAddress = auth()->user()->shippingAddress()->with('address')->orderBy('is_default', 'desc')->get();

        return view('mobile.shipping-address.index', compact('shippingAddress'));
    }


    /**
     * 添加收货地址
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $shippingAddress = new ShippingAddress();
        return view('mobile.shipping-address.shipping-address', compact('shippingAddress'));
    }

    /**
     * 编辑收货地址
     *
     * @param $shippingAddress
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($shippingAddress)
    {
        return view('mobile.shipping-address.shipping-address', compact('shippingAddress'));
    }
}
