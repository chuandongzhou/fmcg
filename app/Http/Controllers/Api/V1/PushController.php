<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\PushDevice;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class PushController extends Controller
{
    /**
     * 注册设备
     *
     * @param \App\Http\Requests\Api\V1\CreatePushDeviceRequest $request
     */
    public function postActiveToken(Requests\Api\V1\CreatePushDeviceRequest $request)
    {
        $attributes = $request->all();
        $service = PushDevice::where('token', $attributes['token'])->find();
        $attributes['type'] = cons('type.push_device')[strtolower($attributes['type'])];
        $attributes['user_id'] = auth()->user()->id;

        isset($service->id) ? $service->fill($attributes)->save() : PushDevice::create($attributes);
    }

    /**
     * 删除设备
     *
     * @param \App\Http\Requests $requests
     */
    public function deleteDeactiveToken(Requests $requests)
    {
        $token = $requests->input('token');
        PushDevice::where('user_id', auth()->user()->id)->where('token', $token)->delete();
    }
}
