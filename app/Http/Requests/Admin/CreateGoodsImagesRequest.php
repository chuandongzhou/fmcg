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
            'level1' => 'required|numeric|min:1',
            'level2' => 'required|numeric|min:1',
            'level3' => 'numeric|min:1',
            'image' => 'required',
            'attrs' => 'required'
        ];
    }
}
