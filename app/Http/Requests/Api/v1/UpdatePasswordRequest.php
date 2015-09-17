<?php

namespace App\Http\Requests\Api\v1;


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
            'old_password'=>'required|min:6|different:password',
            'password'=>'required|min:6|confirmed'
        ];
    }
}
