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
            'pid' => 'sometimes|required|exists:attr,pid',
            'category_id' => 'sometimes|required|exists:category,id'
        ];
    }
}
