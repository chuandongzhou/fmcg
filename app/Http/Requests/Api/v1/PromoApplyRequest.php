<?php

namespace App\Http\Requests\Api\v1;

use App\Http\Requests\Request;

class PromoApplyRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'promo_id' => 'required|integer|exists:promo,id', //资产ID
            'client_id' => 'required|integer|exists:salesman_customer,id', //客户ID
            'apply_remark' => 'string', //申请备注
        ];
    }
}
