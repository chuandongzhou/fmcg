<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $cate = $request->input('cate', 0);
        return view('mobile.category.index', compact('cate'));
    }

}
