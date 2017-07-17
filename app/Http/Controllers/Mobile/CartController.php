<?php

namespace App\Http\Controllers\Mobile;


use App\Services\CartService;

class CartController extends Controller
{

    public function index()
    {
        $myCarts = auth()->user()->carts();
        $carts = $myCarts->with(['goods' => function ($query) {
            $query->select([
                'id',
                'bar_code',
                'name',
                'price_retailer',
                'price_retailer_pick_up',
                'pieces_retailer',
                'min_num_retailer',
                'specification_retailer',
                'price_wholesaler',
                'price_wholesaler_pick_up',
                'pieces_wholesaler',
                'min_num_wholesaler',
                'specification_wholesaler',
                'shop_id',
                'status',
                'user_type'
            ]);
        }, 'goods.shop.user'])->get()->each(function($item){
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
