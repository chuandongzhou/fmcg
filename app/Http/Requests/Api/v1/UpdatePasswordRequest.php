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
            'password' => 'required|min:6|alpha_num|confirmed'
        ];
    }
}
