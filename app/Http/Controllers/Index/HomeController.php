<?php

namespace App\Http\Controllers\Index;

class HomeController extends Controller
{

    /**
     * 首页
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('index.home.index');
    }
}
