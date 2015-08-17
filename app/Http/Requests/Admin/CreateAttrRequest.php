<?php

namespace App\Http\Requests\Admin;


class CreateAttrRequest extends Request
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
            'pid' => 'required|numeric',
            'category_id' => 'required|numeric'
        ];
    }
}
