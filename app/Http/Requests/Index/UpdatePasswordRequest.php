<?php

namespace App\Http\Requests\Index;

use App\Http\Requests\Request;

class UpdatePasswordRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_password'=>'required|min:6',
            'new_password'=>'required|min:6|confirmed'
        ];
    }
}
