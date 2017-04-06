<?php

namespace App\Http\Requests\Admin;


class PayPasswordRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'pay_password' => 'required|alpha_num|min:6'
        ];
    }
}
