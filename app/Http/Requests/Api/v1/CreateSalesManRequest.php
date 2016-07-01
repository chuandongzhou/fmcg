<?php

namespace App\Http\Requests\Api\v1;


class CreateSalesManRequest extends SalesmanRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'avatar' => 'sometimes|required',
            'account' => 'required|between:4,18|unique:salesman',
            'password' => 'required|between:6,18|confirmed',
            'name' => 'required'
        ];
    }
}
