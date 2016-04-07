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
    public function getGoods()
    {
        return $this->success(['goodsColumns' => GoodsService::getNewGoodsColumn()]);
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
        $goods = Goods::active()->with('images.image')->select([
            'id',
            'name',
            'sales_volume',
            'price_retailer',
            'price_wholesaler',
            'bar_code',
            'is_new',
            'is_promotion',
            'is_out',
            'cate_level_1',
            'cate_level_2'
        ])->where('user_type', '>', auth()->user()->type);

        $result = GoodsService::getGoodsBySearch($gets, $goods, false);

        return $this->success([
            'goods' => $result['goods']->paginate()->toArray(),
            'categories' => $result['categories']
        ]);
    }

    /**
     * 商品详情
     *
     * @param $goodsId
     * @return \Illuminate\View\View
     */
    public function postDetail($goodsId)
    {
        $goods = Goods::active()->with(['images.image', 'deliveryArea'])->find($goodsId);
        if (Gate::denies('validate-goods', $goods)) {
            return $this->forbidden('权限不足');
        }
        $attrs = (new AttrService())->getAttrByGoods($goods, true);
        $isLike = auth()->user()->likeGoods()->where('id', $goods->id)->pluck('id');
        $goods->shop_name = $goods->shop()->pluck('name');
        is_null($goods->specification_retailer) && $goods->specification_retailer = '';
        is_null($goods->specification_wholesaler) && $goods->specification_wholesaler = '';
        $goods->attrs = $attrs;
        $goods->is_like = $isLike ? true : false;

        return $this->success(['goods' => $goods]);
    }
}