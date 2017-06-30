<?php

namespace App\Http\Controllers\Mobile;


class MineController extends Controller
{
    /**
     * 我的
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $shop = auth()->user()->shop;
        return view('mobile.mine.index', compact('shop'));
    }

}
