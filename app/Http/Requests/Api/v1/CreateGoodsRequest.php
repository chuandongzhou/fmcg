<?php

namespace App\Http\Requests\Api\v1;


class CreateGoodsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'price_retailer' => 'required|numeric|min:0',
            'min_num_retailer' => 'required|numeric|min:0',
            'price_wholesaler' => 'sometimes|required|numeric|min:0',
            'min_num_wholesaler' => 'sometimes|required|numeric|min:0',
            'cate_level_1' => 'required|numeric|min:0',
            'cate_level_2' => 'required|numeric|min:1',
            'is_new' => 'required|boolean',
            'is_out' => 'required|boolean',
            'is_change' => 'sometimes|required',
            'is_back' => 'sometimes|required',
            'is_expire' => 'required|boolean',
            'is_promotion' => 'required|boolean',
            'promotion_info' => 'sometimes|required_if:is_promotion,1',
            'images' => 'sometimes|array',
            'area' => 'sometimes|array'
        ];
    }
}
