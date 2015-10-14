<?php

namespace App\Http\Requests\Admin;


class UpdateHomeColumnRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $HomeColumn = $this->route('column');
        return [
            'name' => 'sometimes|required|alpha_dash|between:4,16|unique:home_column,name,' . $HomeColumn->id,
            'type' => 'required|in:1,2',
            'id_list' => 'required',
            'sort' => 'required'
        ];
    }
}
