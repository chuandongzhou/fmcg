<?php

namespace App\Http\Controllers\Api\V1\ChildUser;

use App\Http\Requests;
use App\Http\Controllers\Api\V1\Controller;

class DeliveryAreaController extends Controller
{

    protected $shop;

    public function __construct()
    {
        $this->shop = child_auth()->user()->shop;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deliveryArea = $this->shop->deliveryArea->each(function($area){
            $area->setAppends(['min_money'])->setHidden(['extra_common_param']);
        });
        return $this->success(['areas' => $deliveryArea]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\UpdateShopDeliveryAreaRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function store(Requests\Api\v1\UpdateShopDeliveryAreaRequest $request)
    {
        $attributes = $request->all();
        return $this->shop->deliveryArea()->create($attributes)->exists ? $this->success('添加配送区域成功') : $this->error('添加配送区域时出现问题');
    }

    /**
     * 更新配送区域
     *
     * @param \App\Http\Requests\Api\v1\UpdateShopDeliveryAreaRequest $request
     * @param $id
     * @return mixed
     */
    public function update(Requests\Api\v1\UpdateShopDeliveryAreaRequest $request, $id)
    {
        $deliveryArea = $this->shop->deliveryArea()->find($id);
        if (is_null($deliveryArea)) {
            return $this->error('配送区域不存在');
        }
        $attributes = $request->all();
        return $deliveryArea->fill($attributes)->save() ? $this->success('修改配送区域成功') : $this->error('修改配送区域时出现问题');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deliveryArea = $this->shop->deliveryArea()->find($id);
        if (is_null($deliveryArea)) {
            return $this->error('配送区域不存在');
        }

        return $deliveryArea->delete() ? $this->success('删除配送区域成功') : $this->error('删除配送区域时出现问题');
    }
}
