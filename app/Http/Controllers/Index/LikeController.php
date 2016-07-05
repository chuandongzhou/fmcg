<?php

namespace App\Http\Controllers\Index;

use App\Http\Requests;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    protected $userId;

    public function __construct()
    {
        $this->userId = auth()->id();
    }

    /**
     * 获取收藏的店铺信息
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getShops(Request $request)
    {
        $data = $request->all();
        $shops = auth()->user()->likeShops()->with('images')->select(['id', 'name', 'min_money']);
        if (isset($data['name'])) {
            $shops = $shops->where('name', 'like', '%' . $data['name'] . '%');
        }
        if ($request->has('province_id')) {
            $shops = $shops->OfDeliveryArea(array_filter($data));
        }
        $shops = $shops->paginate();
        $shops->each(function ($shop) {
            $shop->setAppends(['image_url', 'sales_volume']);
        });

        return view('index.like.shop', [
            'shops' => $shops,
            'data' => $data
        ]);
    }

    /**
     * 获取收藏的商品信息
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getGoods(Request $request)
    {
        $data = $request->all();
        $goods = auth()->user()->likeGoods()->with('images.image');

        if (isset($data['name'])) {
            $goods = $goods->where('name', 'like', '%' . $data['name'] . '%');
        }
        //获取需要显示的分类ID
        $array = array_unique($goods->lists('cate_level_2')->all());

        $cateArr = array_where(CategoryService::getCategories(), function ($key, $cate) use ($array) {
            return in_array($cate['id'], $array);
        });
        //加入分类过滤条件
        if (!empty($data['cate_level_2'])) {
            $goods = $goods->ofCategory($data['cate_level_2'], 2);
        }
        if (!empty($data['province_id'])) {
            $goods = $goods->OfDeliveryArea($data);
        }

        return view('index.like.goods', [
            'goods' => $goods->orderBy('id', 'DESC')->paginate(16),
            'data' => $data,
            'cateArr' => $cateArr
        ]);
    }
}
