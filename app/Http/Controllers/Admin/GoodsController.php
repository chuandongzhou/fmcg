<?php
namespace App\Http\Controllers\Admin;


class GoodsController extends Controller
{
    /**
     * 商品导入
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function import()
    {
        return view('admin.goods.index');
    }

}