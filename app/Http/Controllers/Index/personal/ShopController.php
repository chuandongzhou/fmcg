<?php

namespace App\Http\Controllers\Index\personal;

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
        //TODO: user_id修改
        $shop = Shop::with('images')->where('user_id', 1)->first(); //商店详情
        return view('index.personal.shop', ['shop' => $shop]);
    }


}
