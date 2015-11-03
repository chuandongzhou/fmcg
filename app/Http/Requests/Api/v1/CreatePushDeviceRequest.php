<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Request;

class CreatePushDeviceRequest extends Request
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
}
