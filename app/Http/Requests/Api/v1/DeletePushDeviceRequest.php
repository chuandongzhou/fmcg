<?php
/**
 * Created by PhpStorm.
 * User: wh
 * Date: 2015/9/30
 * Time: 11:14
 */

namespace app\Http\Requests\Api\v1;



class DeletePushDeviceRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required|unique:push_device',
        ];
    }
}