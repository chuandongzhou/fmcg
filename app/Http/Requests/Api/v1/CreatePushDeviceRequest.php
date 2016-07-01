<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Request;

class CreatePushDeviceRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required',
            'version' => 'required',
            'type' => 'required'    //设备类型iphone=>iPhone设备,android=>android设备
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'type' => '设备类型'
        ];
    }
}
