<?php

namespace App\Http\Requests\Index;


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
            'nickname' => 'required|unique:user,nickname,' . $shop->id,
            'contact_person' => 'required|max:10',
            'contact_info' => 'required',
            'introduction' => 'max:200',
            'address' => 'required|max:60',
            'area' => 'sometimes|required|max:200',
        ];
    }
}
