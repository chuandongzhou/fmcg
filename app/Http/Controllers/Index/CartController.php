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
        $this->middleware('supplier');
    }

    public function index()
    {
        $user = auth()->user();
        $myCarts = $user->carts();
        $carts = $myCarts->whereHas('goods', function ($query) use ($user) {
            $query->whereNotNull('id')->where('user_type', '>', $user->type);
        })->with('goods.images.image')->get();

        (new CartService)->set($carts->count());
        if (!$carts->isEmpty()) {
            // 将所有状态更新为零
            $myCarts->update(['status' => 0]);

            $carts = (new CartService($carts))->formatCarts();
        }
        return view('index.cart.index', ['shops' => $carts]);
    }

}
