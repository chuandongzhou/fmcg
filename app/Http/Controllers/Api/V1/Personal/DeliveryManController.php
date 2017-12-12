<?php

namespace App\Http\Controllers\Api\V1\Personal;

use App\Http\Controllers\Api\v1\Controller;

use App\Http\Requests;
use App\Models\DeliveryMan;
use App\Models\UserBank;
use Illuminate\Http\Request;

class DeliveryManController extends Controller
{

    /**
     * DeliveryManController constructor.
     */
    public function __construct()
    {
        $this->middleware('deposit');
    }

    /**
     * 获取配送人员信息
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function index()
    {
        $deliveryMan = auth()->user()->shop->deliveryMans;

        return $this->success(['delivery_man' => $deliveryMan]);
    }

    /**
     * 添加配送人员
     *
     * @param \App\Http\Requests\Api\v1\CreateDeliveryManRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function store(Requests\Api\v1\CreateDeliveryManRequest $request)
    {
        $attributes = $request->only(['user_name', 'password', 'pos_sign', 'name', 'phone']);
        if (auth()->user()->shop->deliveryMans()->create(array_filter($attributes))->exists) {
            return $this->success('添加成功');
        }

        return $this->error('添加配送人员时出现问题');
    }

    /**
     * 保存
     *
     * @param \App\Http\Requests\Api\v1\UpdateDeliveryManRequest $request
     * @param $deliveryMan
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function update(Requests\Api\v1\UpdateDeliveryManRequest $request, $deliveryMan)
    {
        if ($deliveryMan->fill($request->only(['password', 'pos_sign', 'name', 'phone', 'status']))->save()) {
            return $this->success('保存成功');
        }

        return $this->error('保存失败');
    }

    /**
     * 删除
     *
     * @param $deliveryMan
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function destroy($deliveryMan)
    {
        return $this->success('功能作废');
    }

    /**
     * 启用or禁用
     *
     * @param $deliveryMan
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function status($deliveryMan)
    {
        $dispatchTruck = $deliveryMan->dispatchTruck()->where('status', '<=',
            cons('dispatch_truck.status.delivering'))->first();
        if ($dispatchTruck) {
            return $this->error('被占用!请等待');
        }
        $deliveryMan->status = (int)!$deliveryMan->status;
        $deliveryMan->save();
        return $this->success($deliveryMan->status ? 'success' : null);
    }
}
