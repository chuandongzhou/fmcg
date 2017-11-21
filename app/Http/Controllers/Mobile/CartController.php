<?php

namespace App\Http\Controllers\Mobile;


use App\Services\CartService;

class CartController extends Controller
{

    public function index()
    {
        $myCarts = auth()->user()->carts();
        $carts = $myCarts->with('goods.shop.user')->get()->each(function($item){
            $item->goods->setAppends(['image_url', 'price']);
        });

        if (!$carts->isEmpty()) {
            // 将所有状态更新为零
            $myCarts->update(['status' => 0]);

            $carts = (new CartService($carts))->formatCarts();
        };
        return view('mobile.cart.index', compact('carts'));
    }

}
