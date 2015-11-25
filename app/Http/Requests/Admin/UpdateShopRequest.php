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
        $shop = $this->route('shop');
        return [
            'logo' => 'sometimes|required',
            'name' => 'required|unique:shop,name,' . $shop->id,
            'contact_person' => 'required|max:10',
            'contact_info' => ['required' , 'regex:/^(0?1[0-9]\d{9})$|^((0(10|2[1-9]|[3-9]\d{2}))-?[1-9]\d{6,7})$/'],
            'introduction' => 'max:200',
            'address' => 'required|max:60',
            'area' => 'sometimes|required|max:200',
        ];
    }
}
