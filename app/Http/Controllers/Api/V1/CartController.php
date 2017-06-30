<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/6
 * Time: 17:57
 */

namespace App\Http\Controllers\Api\V1;

use App\Models\Goods;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * 购物车首页
     */
    public function getIndex()
    {
        $myCarts = $this->user->carts();
        $carts = $myCarts->with([
            'goods' => function ($query) {
                $query->select([
                    'id',
                    'bar_code',
                    'name',
                    'price_retailer',
                    'price_retailer_pick_up',
                    'pieces_retailer',
                    'min_num_retailer',
                    'specification_retailer',
                    'price_wholesaler',
                    'price_wholesaler_pick_up',
                    'pieces_wholesaler',
                    'min_num_wholesaler',
                    'specification_wholesaler',
                    'shop_id',
                    'status',
                    'user_type'
                ]);
            },
            'goods.shop.user'
        ])->get()->each(function ($item) {
            $item->goods->setAppends(['image_url', 'price']);
        });

        if (!$carts->isEmpty()) {
            // 将所有状态更新为零
            $myCarts->update(['status' => 0]);

            $carts = (new CartService($carts))->formatCarts();
        }

        return $this->success(['shops' => $carts->values()->toArray()]);
    }

    /**
     * 商品添加至购物车
     *
     * @param \Illuminate\Http\Request $request
     * @param $goodsId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postAdd(Request $request, $goodsId)
    {
        $goodsInfo = Goods::active()->shopUser()->ofSearchType($this->user->type)->find($goodsId);
        if (is_null($goodsInfo)) {
            return $this->error('商品不存在');
        }
        $buyNum = intval($request->input('num'));
        if ($goodsInfo->min_num > $buyNum) {
            return $this->error('不能小于购买量');
        }
        if ($goodsInfo->is_out) {
            return $this->error('该商品缺货');
        }
        if ($buyNum > 20000) {
            return $this->error('商品数量不能大于20000');
        }
        //查询是否有相同的商品,存在则合并
        $cart = $this->user->carts()->where('goods_id', $goodsId);
        if (!is_null($cart->pluck('num'))) {
            if ($cart->pluck('num') + $buyNum <= 20000) {
                $cart->increment('num', $buyNum);
                return $this->success('加入购物车成功');
            } else {
                return $this->error('商品数量不能大于20000');
            }
        }
        if ($this->user->carts()->create(['goods_id' => $goodsId, 'num' => $buyNum])->exists) {
            (new CartService)->increment();
            return $this->success('加入购物车成功');
        }

        return $this->error('加入购物车失败');
    }

    /**
     * 删除购物车商品
     *
     * @param $cartId
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteDelete($cartId)
    {
        $cart = $this->user->carts()->find($cartId);

        if (is_null($cart) || !$cart->delete()) {
            return $this->error('删除失败');
        }

        (new CartService)->decrement();
        return $this->success('删除成功');

    }

    /**
     * 批量删除购物车商品
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     *
     */
    public function deleteBatchDelete(Request $request)
    {

        $ids = $request->input('ids');
        if (is_null($ids)) {
            return $this->error('请选择要删除的商品');
        }
        $field = 'id';
        if ($request->input('type') == 'pc') {
            $field = 'goods_id';
        }
        if ($count = $this->user->carts()->whereIn($field, $ids)->delete()) {
            (new CartService)->decrement($count);
            return $this->success('删除成功');
        }
        return $this->error('删除失败');
    }


    /**
     * 获取购物车数量
     *
     */
    public function getDetail()
    {
        $user = $this->user;
        $myCarts = $user->carts();
        $carts = $myCarts->whereHas('goods', function ($query) use ($user) {
            $query->whereNotNull('id')->ofSearchType($user->type);
        })->with('goods')->get();

        return $this->success(['carts' => $carts]);
    }

}