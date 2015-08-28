<?php

namespace App\Http\Controllers\Index;

use App\Models\Attr;
use App\Models\Category;
use App\Models\Goods;


use App\Http\Requests;
use App\Services\AttrService;

class GoodsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return '添加成功';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $firstCategory = Category::where('pid', 0)->pluck('id');
        $goods = new Goods;
        $goods->cate_level_1 = $firstCategory;
        return view('index.goods.goods', ['goods' => $goods, 'attrs' => []]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($goods)
    {
        return view('index.goods.detail', ['goods' => $goods]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $goods
     * @return Response
     */
    public function edit($goods)
    {
        //获取所有标签
        $attrGoods = [];
        foreach ($goods->attr as $attr) {
            $attrGoods[] = $attr->pivot->toArray();
        }
        $attrIds = array_pluck($attrGoods, 'attr_pid');
        $attrResults = Attr::select(['id', 'pid', 'name'])
            ->whereIn('id', $attrIds)
            ->orWhere(function ($query) use ($attrIds) {
                $query->whereIn('pid', $attrIds);
            })->get()->toArray();
        $attrResults = (new AttrService($attrResults))->format();
        return view('index.goods.goods', ['goods' => $goods, 'attrs' => $attrResults]);
    }

}
