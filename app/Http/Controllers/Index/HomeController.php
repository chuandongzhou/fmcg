<?php

namespace App\Http\Controllers\Index;

use DB;

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
