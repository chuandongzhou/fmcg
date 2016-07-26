<?php

namespace App\Http\Controllers\Index\Business;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Index\Controller;

class MortgageGoodsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $shop = auth()->user()->shop;
        $mortgageGoods = $shop->mortgageGoods()->paginate();

        return view('index.business.mortgage-goods-index', [
            'mortgageGoods' => $mortgageGoods
        ]);
    }

}
