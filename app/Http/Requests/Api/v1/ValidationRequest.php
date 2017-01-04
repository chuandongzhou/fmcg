<?php

namespace App\Http\Requests\Api\v1;
use App\Http\Requests\Request;

 class ValidationRequest extends Request
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'geetest_challenge' => 'geetest'
        ];
    }

    /**
     * Get validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'geetest' => 'Validation Failed'
        ];
    }
}