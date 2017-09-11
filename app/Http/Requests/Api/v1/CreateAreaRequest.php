<?php

namespace App\Http\Requests\Api\v1;


class CreateAreaRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:50',
            'remark' => 'required|max:100'
        ];
    }
}
