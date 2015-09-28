<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/18
 * Time: 11:35
 */
namespace App\Http\Controllers\Api\V1;

use App\Services\AttrService;
use Gate;
use App\Models\Goods;
use App\Services\GoodsService;
use Illuminate\Http\Request;

class GoodsController extends Controller
{
    /**
     * 获取热门商品列表
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function anyHotGoods()
    {
        $type = auth()->user()->type;
        //热门商品
        $hotGoods = Goods::hot()->where('user_type', '>', $type)->select([
            'id',
            'name',
            'price_retailer',
            'price_wholesaler',
            'is_new',
            'is_promotion'
        ])->simplePaginate()->toArray();
        return $this->success($hotGoods);
    }

    /**
     * 商品搜索
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postSearch(Request $request)
    {
        $gets = $request->all();
        $result = GoodsService::getGoodsBySearch($gets, '>');
        return $this->success($result['goods']->simplePaginate()->toArray());
    }

    /**
     * 商品详情
     *
     * @param $goodsId
     * @return \Illuminate\View\View
     */
    public function postDetail($goodsId)
    {
        $goods = Goods::with(['images', 'deliveryArea'])->find($goodsId);
        if (Gate::denies('validate-goods', $goods)) {
            return $this->forbidden('权限不足');
        }
        $attrs = (new AttrService())->getAttrByGoods($goods, true);
        $isLike = auth()->user()->whereHas('likeGoods', function ($q) use ($goods) {
            $q->where('id', $goods->id);
        })->pluck('id');
        $goods->attrs = $attrs;
        $goods->is_like = $isLike ? true : false;
        dd($goods->toArray());
        return $this->success($goods);
    }
}