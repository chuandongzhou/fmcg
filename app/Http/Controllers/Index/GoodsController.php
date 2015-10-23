<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/6
 * Time: 15:54
 */
namespace App\Http\Controllers\Index;

use App\Services\AttrService;
use Gate;

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
            abort(403);
        }
        $attrs = (new AttrService())->getAttrByGoods($goods, true);
        $goods->load('images', 'images')->load('deliveryArea', 'deliveryArea');
        $isLike = auth()->user()->whereHas('likeGoods', function ($q) use ($goods) {
            $q->where('id', $goods->id);
        })->pluck('id');
        $coordinate = $goods->deliveryArea->each(function ($area) {
            $area->coordinate;
        });

        return view('index.goods.detail', [
            'goods' => $goods,
            'attrs' => $attrs,
            'isLike' => $isLike,
            'coordinates' => $coordinate->toJson()
        ]);
    }
}