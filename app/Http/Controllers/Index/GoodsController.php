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
        $attrs = (new AttrService())->getAttrByGoods($goods);
        $goods->load('images', 'images')->load('deliveryArea', 'deliveryArea');
        $isLike = $goods->likes()->where('user_id', auth()->user()->id)->pluck('id');
        return view('index.goods.detail', [
            'goods' => $goods,
            'attrs' => $attrs,
            'isLike' => $isLike
        ]);
    }
}