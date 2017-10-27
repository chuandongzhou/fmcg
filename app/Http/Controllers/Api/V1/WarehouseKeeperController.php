<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\v1\CreateWareHouseKeeperRequest;
use App\Http\Requests\Api\v1\UpdateWareHouseKeeperRequest;
use Illuminate\Http\Request;
use App\Models\WarehouseKeeper;
use Gate;

class WarehouseKeeperController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\CreateWareHouseKeeperRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function store(CreateWareHouseKeeperRequest $request)
    {
        return auth()->user()->shop->warehouseKeepers()->create($request->all()) ? $this->success('添加成功') : $this->error('添加仓管员是出现问题');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\UpdateWareHouseKeeperRequest $request
     * @param \App\Models\WarehouseKeeper $warehouseKeeper
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function update(UpdateWareHouseKeeperRequest $request, WarehouseKeeper $warehouseKeeper)
    {

        if (Gate::denies('validate-warehouse-keeper', $warehouseKeeper)) {
            return $this->error('仓管员不存在');
        }
        return $warehouseKeeper->fill($request->all())->save() ? $this->success('修改成功') : $this->error('修改仓管员是出现问题');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\WarehouseKeeper $warehouseKeeper
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function destroy(WarehouseKeeper $warehouseKeeper)
    {
        if (Gate::denies('validate-warehouse-keeper', $warehouseKeeper)) {
            return $this->error('仓管员不存在');
        }
        return $warehouseKeeper->delete() ? $this->success('删除仓管员成功') : $this->error('删除仓管员是出现问题');
    }

    /**
     * change warehouse-keeper status
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\WarehouseKeeper $warehouseKeeper
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function status(Request $request, WarehouseKeeper $warehouseKeeper)
    {
        if (Gate::denies('validate-warehouse-keeper', $warehouseKeeper)) {
            return $this->error('仓管员不存在');
        }
        $status = intval($request->input('status'));
        $warehouseKeeper->status = $status;
        if ($warehouseKeeper->save()) {
            if ($status) {
                return $this->success('操作成功');
            } else {
                return $this->success(null);
            }
        }
        return $this->error('操作失败');
    }

}
