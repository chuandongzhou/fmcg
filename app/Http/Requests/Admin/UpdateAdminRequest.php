<?php

namespace App\Http\Requests\Admin;


class UpdateAdminRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $admin = $this->route('admin');
        return [
            'name' => 'required|max:20|unique:admin,name,' . $admin->id,
            'real_name' => 'required|max:32|min:2',
            'password' => 'min:6|confirmed',
        ];
    }
}
