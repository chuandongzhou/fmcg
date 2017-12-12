<?php

namespace App\Http\Controllers\Api\V1\Personal;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\v1\CreateDeliveryTruckRequest;
use App\Http\Requests\Api\v1\UpdateDeliveryTruckRequest;
use App\Models\DeliveryTruck;
use Illuminate\Http\Request;
use Gate;

class DeliveryTruckController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\CreateDeliveryTruckRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function store(CreateDeliveryTruckRequest $request)
    {
        return auth()->user()->shop->deliveryTrucks()->create($request->all()) ? $this->success('添加成功') : $this->error('添加配送车辆时出现问题');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\UpdateDeliveryTruckRequest $request
     * @param \App\Models\DeliveryTruck $deliveryTruck
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function update(UpdateDeliveryTruckRequest $request, DeliveryTruck $deliveryTruck)
    {

        if (Gate::denies('validate-warehouse-keeper', $deliveryTruck)) {
            return $this->error('配送车辆不存在');
        }
        return $deliveryTruck->fill($request->all())->save() ? $this->success('修改成功') : $this->error('修改配送车辆时出现问题');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\DeliveryTruck $deliveryTruck
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function destroy(DeliveryTruck $deliveryTruck)
    {
        return $this->success('功能作废');
        /*if (Gate::denies('validate-warehouse-keeper', $deliveryTruck)) {
            return $this->error('配送车辆不存在');
        }
        return $deliveryTruck->delete() ? $this->success('删除配送车辆成功') : $this->error('删除配送车辆时出现问题');*/
    }

    /**
     * change warehouse-keeper status
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\DeliveryTruck $deliveryTruck
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function status(Request $request, DeliveryTruck $deliveryTruck)
    {
        if (Gate::denies('validate-warehouse-keeper', $deliveryTruck)) {
            return $this->error('配送车辆不存在');
        }
        $status = intval($request->input('status'));
        if ($deliveryTruck->status > cons('truck.status.spare_time')) {
            return $this->error('车辆正在使用,无法更改状态!');
        }
        $deliveryTruck->status = $status;
        if ($deliveryTruck->save()) {
            if ($status) {
                return $this->success('操作成功');
            } else {
                return $this->success(null);
            }
        }
        return $this->error('操作失败');
    }
}
