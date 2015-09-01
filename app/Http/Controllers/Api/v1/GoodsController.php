<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Goods;

use App\Http\Requests;
use App\Services\AddressService;
use Illuminate\Http\Request;
use App\Models\Address;

class GoodsController extends Controller
{
    /**
     * tore a newly created resource in storage.
     *
     * @param \App\Http\Requests\Index\CreateGoodsRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Requests\Index\CreateGoodsRequest $request)
    {
        $attributes = $request->all();
        $goods = Goods::create($attributes);
        if ($goods->exists) {
            // 更新配送地址
            $this->updateDeliveryArea($goods, $request->input('area'));

            // 更新标签
            $this->updateAttrs($goods, $attributes['attrs']);
            return $this->created('添加商品成功');
        }
        return $this->error('添加商品出现错误');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Index\UpdateGoodsRequest $request
     * @param $goods
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Requests\Index\UpdateGoodsRequest $request, $goods)
    {
        $attributes = $request->all();
        if ($goods->fill($attributes)->save()) {
            // 更新配送地址
            $this->updateDeliveryArea($goods, $attributes['area']);
            // 更新标签
            $this->updateAttrs($goods, $attributes['attrs']);
            return $this->success('更新商品成功');
        }
        return $this->error('更新商品时遇到问题');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $goods
     * @return Response
     */
    public function destroy($goods)
    {
        if ($goods->delete()) {
            return $this->success('删除商品成功');
        }
        return $this->error('删除商品时遇到问');
    }

    /**
     * 商品上下架
     *
     * @param \Illuminate\Http\Request $request
     * @param $goodsId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function shelve(Request $request, $goodsId)
    {
        $goods = Goods::find($goodsId);
        $status =  intval($request->input('status'));
        $goods->status = $status;
        $statusVal = cons()->valueLang('goods.status' ,$status);
        if ($goods->save()) {
           return $this->success('商品' . $statusVal . '成功');
        }
        return $this->error('商品' . $statusVal . '失败');
    }

    /**
     * 更新配送地址处理
     *
     * @param $model
     * @param $area
     * @return bool
     */
    private function updateDeliveryArea($model, $area)
    {
        //配送区域添加
        $areaArr = (new AddressService($area))->formatAddressPost();

        $nowArea = $model->deliveryArea;
        if (count($nowArea) == count(array_filter($area['province_id']))) {
            return true;
        }
        $model->deliveryArea()->delete();
        if (!empty($areaArr)) {
           $data = [];
            foreach ($areaArr as $area) {
                $data[] = new Address($area);
            }
            $model->deliveryArea()->saveMany($data);
        }
        return true;
    }

    /**
     * 更新标签
     *
     * @param $model
     * @param $attrs
     */
    private function updateAttrs($model, $attrs)
    {
        //删除所有标签
        $model->attr()->detach();

        $attrArr = [];
        foreach ($attrs as $pid => $id) {
            $attrArr[$id] = ['attr_pid' => $pid];
        }
        if (!empty($attrArr)) {
            $model->attr()->sync($attrArr);
        }
    }
}
