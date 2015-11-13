<?php

namespace App\Http\Requests\Admin;


class CreateHomeColumnRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|alpha_dash|between:4,16|unique:home_column',
            'type' => 'required|in:1,2',
            'id_list' => 'required',
            'sort' => 'required'
        ];
    }

    /**
     * @return array
     */

    public function attributes()
    {
        return [
            'name' => '栏目名'
        ];
    }
}
