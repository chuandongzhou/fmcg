<?php

namespace App\Http\Controllers\ChildUser;


class ShopController extends Controller
{
    /**
     * 商家信息
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $shop = child_auth()->user()->shop;
        return view('child-user.shop.index', ['shop' => $shop]);
    }
}
