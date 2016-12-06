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
        if (Gate::denies('validate-goods', $goods)) {
            return redirect(url('search'));
        }
        $attrs = (new AttrService())->getAttrByGoods($goods, true);
        $goods->load('images.image')->load('deliveryArea');
        $isGoodsLike = auth()->user()->likeGoods()->where('id', $goods->id)->pluck('id');
        $shop_id = $goods->shop->id;
        $isLike  = auth()->user()->likeShops()->where('shop_id', $shop_id)->pluck('id');
        $couponNum = CouponService::shopCouponNum($shop_id);
        $hotGoods = Goods::where('shop_id', $shop_id)->active()->with('images.image')->ofCommonSort()->orderBy('id',
            'DESC')->take(5)->get();
        /*  $coordinate = $goods->deliveryArea->each(function ($area) {
              $area->coordinate;
          });*/
        return view('index.goods.detail', [
            'goods' => $goods,
            'attrs' => $attrs,
            'isLike' => $isLike,
            'categoriesName' => GoodsService::getGoodsCate($goods),
            'couponNum' => $couponNum,
            'hotGoods' => $hotGoods,
            'shop' => $goods->shop,
            'isGoodsLike' => $isGoodsLike
            /*   'coordinates' => $coordinate->toJson()*/
        ]);
    }
}