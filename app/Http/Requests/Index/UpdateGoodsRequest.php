<?php

namespace App\Http\Requests\Index;


class UpdateGoodsRequest extends Request
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
            'price' => 'required|numeric|min:0',
            'cate_level_1' => 'required|numeric|min:0',
            'cate_level_2' => 'required|numeric|min:1',
            'is_new' => 'required|boolean',
            'is_out' => 'required|boolean',
            'is_change' => 'sometimes|required|accepted',
            'is_back' => 'sometimes|required|accepted',
            'is_expire' => 'required|boolean',
            'is_promotion' => 'required|boolean',
            'promotion_info' => 'sometimes|required_if:is_promotion,1',
            'min_num' => 'required|numeric|min:0',
            'images' => 'sometimes|array',
            'area' => 'sometimes|array'
        ];
    }
}
