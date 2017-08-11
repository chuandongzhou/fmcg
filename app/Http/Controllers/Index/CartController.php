<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/7
 * Time: 14:29
 */
namespace App\Http\Controllers\Index;

use App\Services\CartService;
use Cache;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('forbid.only_seller');
    }

    public function index()
    {
        $user = auth()->user();
        $myCarts = $user->carts();
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
        }, 'goods.shop.user'])->get();

        (new CartService)->set($carts->count());
        if (!$carts->isEmpty()) {
            // 将所有状态更新为零
            $myCarts->update(['status' => 0]);

            $carts = (new CartService($carts))->formatCarts();
        }
        return view('index.cart.index', ['shops' => $carts]);
    }

}
