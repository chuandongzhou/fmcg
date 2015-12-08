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
            'bar_code' => 'required|digits_between:13,18',
            'images' => 'required',
        ];
    }
}
