<?php

namespace App\Http\Requests\Admin;


class CreateCategoryRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'pid' =>'required|exists:category,pid',
            'name'=>'required|unique:category'
        ];
    }
}
