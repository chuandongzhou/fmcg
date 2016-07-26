<?php

namespace App\Http\Requests\Api\v1;


class UpdateSalesmanRequest extends SalesmanRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
       // $salesman = $this->route('salesman');
        return [
            'avatar' => 'sometimes|required',
            //'account' => 'required|between:4,18|unique:salesman,account,' . $salesman->id,
            'password' => 'between:6,18|confirmed',
            'name' => 'required',
            'contact_information' => 'required'
        ];
    }
}
