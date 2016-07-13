<?php
/**
 * Created by PhpStorm.
 * User: wh
 * Date: 2015/9/30
 * Time: 11:14
 */

namespace App\Http\Requests\Api\v1;



class DeletePushDeviceRequest extends UserRequest
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
        ];
    }
}