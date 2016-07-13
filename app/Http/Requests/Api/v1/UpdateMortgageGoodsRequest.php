<?php

namespace App\Http\Requests\Api\v1;


class UpdateMortgageGoodsRequest extends SalesmanRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'goods_name' => 'required',
            'pieces' => 'required',
        ];
    }
}
