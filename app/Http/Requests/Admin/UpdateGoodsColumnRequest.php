<?php

namespace App\Http\Requests\Admin;


class UpdateGoodsColumnRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'province_id'=> 'required|integer',
            'cate_level_1' => 'required',
            'id_list' => 'required',
        ];
    }
}
