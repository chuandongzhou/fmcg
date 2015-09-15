<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/6
 * Time: 17:57
 */

namespace App\Http\Controllers\Api\v1;

use App\Models\Goods;
use Auth;
use Illuminate\Http\Request;

class CartController extends Controller
{

    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function postAdd(Request $request, $goodsId)
    {
        $goodsInfo = Goods::where('user_type', '>', $this->user->type)->find($goodsId);
        if (is_null($goodsInfo)) {
            return $this->error('商品不存在');
        }
        $buyNum = intval($request->input('num'));
        if ($goodsInfo->min_num > $buyNum) {
            return $this->error('不能小于购买量');
        }

        /**
         * 查询是否有相同的商品
         */
        $cart = $this->user->carts()->where('goods_id', $goodsId);
        if (!is_null($cart->pluck('id'))) {
            $cart->increment('num', $buyNum);
            return $this->success('加入购物车成功');
        }
        if ($this->user->carts()->create(['goods_id' => $goodsId, 'num' => $buyNum])->exists) {
            return $this->success('加入购物车成功');
        }
        return $this->error('加入购物车失败');
    }

    /**
     * @param $cartId
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteDelete($cartId)
    {
        $cart = auth()->user()->carts()->find($cartId);

        if (!is_null($cart)) {
            $cart->delete();
        }
        return $this->success('删除成功');
    }


}