<?php

namespace App\Http\Controllers\ChildUser;

class MortgageGoodsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $shop = child_auth()->user()->shop;
        $mortgageGoods = $shop->mortgageGoods()->paginate();

        return view('child-user.mortgage-goods.index', [
            'mortgageGoods' => $mortgageGoods
        ]);
    }

}
