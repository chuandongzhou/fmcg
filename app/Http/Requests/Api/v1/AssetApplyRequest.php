<?php

namespace App\Http\Requests\Api\v1;

class AssetApplyRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'asset_id' => 'required|integer|exists:asset,id',    //资产编号
            'client_id' => 'required|integer|exists:salesman_customer,id',      //客户ID
            'quantity' => 'required|numeric',       //数量
            'use_date' => 'sometimes|date',       //使用时间
        ];
    }
}
