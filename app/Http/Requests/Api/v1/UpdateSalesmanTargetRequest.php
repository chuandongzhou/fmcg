<?php

namespace App\Http\Requests\Api\v1;


class UpdateSalesmanTargetRequest extends SalesmanRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required|date',
            'salesman_id' => 'required|integer',
            'target' => 'required|numeric|min:0',
        ];
    }
}
