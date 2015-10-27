<?php

namespace App\Http\Requests\Admin;


class CreateGoodsImagesRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cate_level_1' => 'required|numeric|min:1',
            'cate_level_2' => 'required|numeric|min:1',
            'cate_level_3' => 'required|numeric|min:1',
            'image' => 'required',
        ];
    }
}
