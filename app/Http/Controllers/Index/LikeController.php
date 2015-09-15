<?php

namespace App\Http\Controllers\Index;

use App\Models\Like;

use App\Http\Requests;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    protected $userId;

    public function __construct()
    {
        $this->userId = Auth()->user()->id;
    }

    /**
     * 获取收藏的店铺信息
     *
     * @return \Illuminate\View\View
     */
    public function getShops(Request $request)
    {
        $shops = Like::with('likeable')->where('user_id', $this->userId)->where('likeable_type',
            cons('model.shop'))->get();

        return view('index.like.shop', [
            'shops' => $shops
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
