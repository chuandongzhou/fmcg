<?php

namespace App\Http\Requests\Api\v1;


class CreateInterestsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
           'status' => 'required|in:0,1',
            'type' =>'required|in:"goods","shops"',
            'id' =>'required|numeric'
        ];
    }
}
