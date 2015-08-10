<?php

namespace App\Http\Requests\Admin;

class CreateRoleRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:role',
        ];
    }
}
