<?php

namespace App\Http\Requests\Api\v1;


class DeleteMortgageGoodsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_id' => 'required|integer|min:1',
            'mortgage_goods_id' => 'required|integer|min:1',
        ];
    }
}
