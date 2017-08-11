<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\PromoGoods;
use App\Services\PromoService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PromoController extends Controller
{
    private $promoService;

    public function __construct()
    {
        $this->promoService = new PromoService();
    }

    /**
     * 获取参加促销的商品
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getGoods(Request $request)
    {
        $data = $request->only('condition', 'page');
        $goods = auth()->user()->shop->promoGoods()->with([
            'goods',
            'goods.goodsPieces'
        ])->OfNameBarCode($data['condition'])->active()->paginate();
        $goods->each(function ($item) {
            $item->goods->goodsPieces->pieces_level_1_lang = cons()->valueLang('goods.pieces',
                $item->goods->goodsPieces->pieces_level_1);
            $item->goods->goodsPieces->pieces_level_2_lang = cons()->valueLang('goods.pieces',
                $item->goods->goodsPieces->pieces_level_2);
            $item->goods->goodsPieces->pieces_level_3_lang = cons()->valueLang('goods.pieces',
                $item->goods->goodsPieces->pieces_level_3);
        });
        return $this->success([
            'goods' => $goods->toArray(),
        ]);
    }

    /**
     * 促销添加
     *
     * @param \App\Http\Requests\Api\v1\CreatePromoRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function add(Requests\Api\v1\CreatePromoRequest $request)
    {
        try {
            $data = $request->all();
            DB::beginTransaction();
            $this->promoService->add($data);
            DB::commit();
            return $this->success('添加成功!');
        } catch (\Exception $e) {
            DB::rollback();
            info($e->getMessage());
            return $this->error('添加出现问题!');
        }
    }

    /**
     * 促销修改
     *
     * @param \App\Http\Requests\Api\v1\CreatePromoRequest $request
     * @param $promo
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function edit(Requests\Api\v1\CreatePromoRequest $request, $promo)
    {
        try {
            if (Gate::denies('validate-shop-promo', $promo)) {
                return $this->error('没有权限!');
            }
            $data = $request->all();
            DB::beginTransaction();
            $this->promoService->edit($promo, $data);
            DB::commit();
            return $this->success('修改成功!');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error('编辑出现问题!');
        }
    }

    /**
     * 状态切换
     *
     * @param \Illuminate\Http\Request $request
     * @param $promo
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function status(Request $request, $promo)
    {
        $data = $request->only('status');
        $action = cons()->valueLang('status', array_get($data, 'status'));
        return $promo->fill($data)->save() ? $this->success($action . '成功') : $this->error($action . '失败');
    }

    /**
     * 促销商品删除
     *
     * @param \App\Models\PromoGoods $goods
     * @return \WeiHeng\Responses\Apiv1Response
     * @throws \Exception
     */
    public function goodsDestroy(PromoGoods $goods)
    {
        return $goods->delete() ? $this->success('删除成功') : $this->error('删除失败');
    }

    /**促销商品启/禁用
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PromoGoods $goods
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function goodsStatus(PromoGoods $goods)
    {
        $goods->status = $goods->status == cons('status.off') ? cons('status.on') : cons('status.off');
        return $goods->save() ? $this->success(cons()->valueLang('status',
                $goods->status) . '成功') : $this->error(cons()->valueLang('status', $goods->status) . '失败');
    }

    /**
     * 促销商品批量删除
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function goodsBatchDestroy(Request $request)
    {
        $ids = $request->input('ids');
        if (count($ids) < 1) {
            return $this->error('请选中至少一个商品');
        }
        return auth()->user()->shop->promoGoods()->whereIn('id',
            $ids)->delete() ? $this->success('操作成功') : $this->error('操作失败');
    }

    /**
     * 促销商品批量启/禁用
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function goodsBatchStatus(Request $request)
    {
        $ids = $request->input('ids');
        $status = $request->input('status');
        if (count($ids) < 1) {
            return $this->error('请选中至少一个商品');
        }
        return auth()->user()->shop->promoGoods()->whereIn('id',
            $ids)->update(['status' => $status]) ? $this->success('操作成功') : $this->error('操作失败');
    }

    /**
     * 通过申请
     *
     * @param $apply
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function applyPass($apply)
    {
        if (Gate::denies('validate-shop-promoApply', $apply)) {
            return $this->error('没有权限!');
        }
        return $apply->fill([
            'status' => cons('promo.review_status.pass'),
            'pass_date' => Carbon::now(),
        ])->save() ? $this->success('通过成功') : $this->error('通过失败');
    }

    /**
     * 删除/拒绝申请
     *
     * @param $apply
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function applyDelete($apply)
    {
        if (Gate::denies('validate-shop-promoApply', $apply)) {
            return $this->error('没有权限!');
        }
        return $apply->delete() ? $this->success('删除成功') : $this->error('删除失败');
    }

    /**
     * 促销申请编辑
     *
     * @param \Illuminate\Http\Request $request
     * @param $apply
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function applyEdit(Request $request, $apply)
    {
        if (Gate::denies('validate-shop-promoApply', $apply)) {
            return $this->error('没有权限!');
        }
        $data = $request->only('apply_remark');
        return $apply->fill($data)->save() ? $this->success('保存成功') : $this->error('保存失败');
    }


    public function partakeOrderDetail($apply)
    {
        if($apply){
            $apply->load(['client.shippingAddress','order.orderGoods.goods'])->client->setAppends(['shipping_address_name']);
            $apply->orderGoodsCount = $apply->order->orderGoods->sum('num');
            $apply->orderGoodsPriceCount = $apply->order->orderGoods->sum('amount');
            return $this->success($apply);
        }
    }
}
