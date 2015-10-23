<?php

namespace App\Http\Requests\Api\v1;


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
            'contact_info' => 'required',
            'min_money'=>'required',
            'introduction' => 'max:200',
            'address' => 'required|max:60',
            'area' => 'sometimes|required|max:200',
            'license' => 'sometimes|required',
            'business_license' => 'sometimes|required',
            'agency_contract' => 'sometimes|required'
        ];
    }
}
