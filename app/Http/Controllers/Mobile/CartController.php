<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return view('mobile.cart.index');
    }

}
