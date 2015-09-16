<?php

namespace App\Http\Controllers\Index\Personal;

use App\Http\Controllers\Index\Controller;
use App\Models\Shop;

use App\Http\Requests;

class ShopController extends Controller
{
    protected $shopId = 1;

    /**
     * 商家信息
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $shop = auth()->user()->shop()->with('images')->first();
        return view('index.personal.shop', ['shop' => $shop]);
    }


}
