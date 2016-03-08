<?php

namespace App\Http\Requests\Admin;


class UpdateCategoryRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $category = $this->route('category');
        return [
            'pid' => 'required',
            'name' => 'required|unique:category,name,' . $category->id
        ];
    }
}
