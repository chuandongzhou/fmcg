<?php

namespace App\Http\Requests\Api\v1;


class UpdateSalesmanVisitOrderGoodsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'num' => 'required|integer|min:1|max:10000',
            'price' => 'sometimes|required|numeric|min:0',
            'amount' => 'sometimes|required|numeric|min:0',
            'pieces' => 'sometimes|required|numeric|min:0',
            'id' => 'required|integer'
        ];
    }
}
