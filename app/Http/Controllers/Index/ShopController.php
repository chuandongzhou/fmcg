<?php

namespace App\Http\Controllers\Index;

use App\Models\Shop;
use DB;

class ShopController extends Controller
{

    /**
     * 首页
     *
     * @return \Illuminate\View\View
     */
    public function getDetail()
    {
        $shop = Shop::where('user_id', 1)->first();
        return view('index.shop.detail', ['shop' => $shop]);
    }

    public function getIndex()
    {
        return 'ee';
    }
}
