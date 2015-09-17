<?php

namespace App\Http\Controllers\Index;

use App\Models\Like;

use App\Http\Requests;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    protected $userId;

    public function __construct()
    {
        $this->userId = auth()->user()->id;
    }

    /**
     * 获取收藏的店铺信息
     *
     * @return \Illuminate\View\View
     */
    public function getShops(Request $request)
    {
        $data = $request->all();
        $shops = auth()->user()->likeShops();
        if (isset($data['name'])) {
            $shops = $shops->where('name', 'like', '%' . $data['name'] . '%');
        }
        if ($data['province_id']) {
            $shops = $shops->OfDeliveryArea($data);
        }

        return view('index.like.shop', [
            'shops' => $shops->paginate(),
            'data' => $data
        ]);
    }

    /**
     * 获取收藏的商品信息
     *
     * @return \Illuminate\View\View
     */
    public function getGoods()
    {
        $goods = Like::with('likeable')->where('user_id', $this->userId)->where('likeable_type',
            cons('model.goods'))->get();

        return view('index.like.goods', [
            'goods' => $goods
        ]);
    }
}
