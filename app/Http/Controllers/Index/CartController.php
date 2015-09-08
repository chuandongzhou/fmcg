<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/7
 * Time: 14:29
 */
namespace App\Http\Controllers\Index;

use App\Services\CartService;

class CartController extends Controller
{

    public function index()
    {
        $carts = auth()->user()->carts()->with('goods')->get();
        if (!empty($carts[0])) {
            $carts =(new CartService($carts))->formatCarts();
        }
        return view('index.cart.index' , ['shops' => $carts]);
    }

}
