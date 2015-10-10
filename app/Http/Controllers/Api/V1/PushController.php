<?php

namespace App\Http\Controllers\Api\V1;

use app\Http\Requests\Api\v1\DeletePushDeviceRequest;
use App\Models\PushDevice;
use App\Http\Requests;
use App\Services\PushOrderService;

class PushController extends Controller
{
    /**
     * 注册设备
     *
     * @param \App\Http\Requests\Api\V1\CreatePushDeviceRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postActiveToken(Requests\Api\V1\CreatePushDeviceRequest $request)
    {
        $attributes = $request->all();
        $service = PushDevice::where('token', $attributes['token'])->find();
        $attributes['type'] = cons('type.push_device')[strtolower($attributes['type'])];
        $attributes['user_id'] = auth()->user()->id;

        $status = isset($service->id) ? $service->fill($attributes)->save() : PushDevice::create($attributes);
        if ($status) {
            return $this->success('注册成功');
        }

        return $this->error('注册失败');
    }

    /**
     * 删除设备
     *
     * @param \app\Http\Requests\Api\v1\DeletePushDeviceRequest $requests
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function deleteDeactiveToken(DeletePushDeviceRequest $requests)
    {
        $token = $requests->input('token');
        $status = PushDevice::where('user_id', auth()->user()->id)->where('token', $token)->delete();
        if ($status) {
            return $this->success('删除成功');
        }

        return $this->error('删除失败');
    }
}
