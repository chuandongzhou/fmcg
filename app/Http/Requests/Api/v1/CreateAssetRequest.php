<?php

namespace App\Http\Requests\Api\v1;


class CreateAssetRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:30',
            'quantity' => 'required|numeric',
            'unit' => 'required|max:2',
            'status' => 'in:0,1',
        ];
    }
}
