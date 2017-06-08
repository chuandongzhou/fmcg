<?php

namespace App\Http\Controllers\Mobile;


use App\Models\Shop;

class SearchController extends Controller
{
    /**
     * 商品搜索
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('mobile.search.index');
    }


    public function shopGoods(Shop $shop)
    {
        return view('mobile.search.shop-goods', compact('shop'));
    }

}
