<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/6
 * Time: 15:54
 */
namespace App\Http\Controllers\Index;

use App\Services\AttrService;
use App\Services\GoodsService;
use Gate;
use App\Models\Goods;
use App\Services\CouponService;

class GoodsController extends Controller
{

    /**
     * 商品详情
     *
     * @param $goods
     * @return \Illuminate\View\View
     */
    public function detail($goods)
    {
        $user = auth()->user();
        if (Gate::denies('validate-goods', $goods)) {
            return redirect(url('search'));
        }
        $attrs = (new AttrService())->getAttrByGoods($goods);
        $goods->load('deliveryArea');
        $isGoodsLike = $user->likeGoods()->where('id', $goods->id)->pluck('id');
        $shopId = $goods->shop->id;
        $isLike  =$user->likeShops()->where('shop_id', $shopId)->pluck('id');
        $couponNum = CouponService::shopCouponNum($shopId);
        $hotGoods = Goods::where('shop_id', $shopId)->active()->ofCommonSort()->orderBy('id',
            'DESC')->take(5)->get();
        return view('index.goods.detail', [
            'goods' => $goods,
            'attrs' => $attrs,
            'isLike' => $isLike,
            'categoriesName' => GoodsService::getGoodsCate($goods),
            'couponNum' => $couponNum,
            'hotGoods' => $hotGoods,
            'shop' => $goods->shop,
            'isGoodsLike' => $isGoodsLike,
            'isMyGoods' => $shopId == $user->shop_id
        ]);
    }
}