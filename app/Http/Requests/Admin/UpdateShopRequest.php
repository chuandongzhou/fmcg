<?php

namespace App\Http\Requests\Admin;


class UpdateShopRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'logo' => 'required',
            'name' => 'required|unique:user',
            'contact_person' => 'required|max:10',
            'contact_info' => 'required',
            'introduction' => 'max:200',
            'address' => 'required|max:60',
            'delivery_area' => 'required|max:200',
        ];
    }
}
