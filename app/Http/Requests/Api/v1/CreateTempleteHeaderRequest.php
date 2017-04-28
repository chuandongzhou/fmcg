<?php

namespace App\Http\Requests\Api\v1;


class CreateTempleteHeaderRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'contact_person' => 'required|max:10',
            'contact_info' => ['required' , 'regex:/^(0?1[0-9]\d{9})$|^((0(10|2[1-9]|[3-9]\d{2}))-?[1-9]\d{6,7})$/'],
            'address' => 'required|max:50',
        ];
    }
}
