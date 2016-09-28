<?php

namespace App\Http\Requests\Api\v1;


class CustomerDisplayFeeRequest extends SalesmanRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'salesman_customer_id' => 'required|numeric',
            'start_at' => 'required|date',
            'end_at' => 'required|date'
        ];
    }
}
