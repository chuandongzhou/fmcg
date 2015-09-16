<?php

namespace App\Http\Controllers\Index;

use App\Models\Like;

use App\Http\Requests;
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
        $address = $request->input('address');dd($address);
        $roleName = trim($request->input('user_name'));
        $shops = User::whereHas('likeShops', function ($query) use ($address, $roleName) {
            if ($address['province_id']) {
                $query->where('province_id', $address['province_id']);
            }
            if ($address['city_id']) {
                $query->where('city_id', $address['city_id']);
            }
            if ($address['country_id']) {
                $query->where('country_id', $address['country_id']);
            }
            if ($roleName) {
                $query->whereHas('likeShops.user', function ($query) use ($roleName) {
                    $query->where('user_name', $roleName);
                });

            }
        })->with('likeShops')->find($this->userId);
        $shops = $shops ? $shops->toArray() : [];


        return view('index.like.shop', [
            'res' => $shops
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
