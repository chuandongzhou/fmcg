<?php

namespace App\Http\Controllers\Api\v1\personal;

use App\Http\Controllers\Api\v1\Controller;

use App\Http\Requests;
use App\Models\DeliveryMan;
use App\Models\UserBank;

class DeliveryManController extends Controller
{
    protected $userId = 1;

    /**
     * 添加配送人员
     *
     * @param \App\Http\Requests\Index\CreateDeliveryManRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function store(Requests\Index\CreateDeliveryManRequest $request)
    {
        // TODO: userId 登录后添加
        if (DeliveryMan::create($request->all())->exists) {
            return $this->success('添加成功');
        }
        return $this->success('添加配送人员时出现问题');
    }

    /**
     * 保存
     *
     * @param \App\Http\Requests\Index\CreateDeliveryManRequest $request
     * @param $userBank
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function update(Requests\Index\CreateDeliveryManRequest $request, $deliveryMan)
    {
        if ($deliveryMan->fill($request->all())->save()) {
            return $this->success('保存成功');
        }
        return $this->success('保存配送人员时出现问题');
    }

    /**
     * 删除
     *
     * @param \App\Models\UserBank $bank
     * @return \WeiHeng\Responses\Apiv1Response
     * @throws \Exception
     */
    public function destroy(UserBank $bank)
    {
        if ($bank->delete()) {
            return $this->success('删除银行账号成功');
        }
        return $this->error('删除时遇到问题');
    }
}
