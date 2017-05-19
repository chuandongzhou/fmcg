<?php

namespace App\Http\Controllers\Index\Personal;

use App\Http\Controllers\Index\Controller;


class ShopController extends Controller
{
    /**
     * 商家信息
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $shop = auth()->user()->shop;
        return view('index.personal.shop', ['shop' => $shop/*, 'coordinates' => $coordinate->toJson()*/]);
    }
}
