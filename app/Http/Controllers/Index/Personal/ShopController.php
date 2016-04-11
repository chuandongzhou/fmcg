<?php

namespace App\Http\Controllers\Index\Personal;

use App\Http\Controllers\Index\Controller;

use App\Http\Requests;

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
       /* $coordinate = $shop->deliveryArea->each(function ($area) {
            $area->coordinate;
        });*/
        return view('index.personal.shop', ['shop' => $shop/*, 'coordinates' => $coordinate->toJson()*/]);
    }


}
