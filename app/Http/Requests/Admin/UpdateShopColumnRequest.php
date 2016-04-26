<?php

namespace App\Http\Requests\Admin;


class UpdateShopColumnRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $shopColumn = $this->route('shop_column');
        return [
            'name' => 'sometimes|required|alpha_dash|between:4,16|unique:shop_column,name,' . $shopColumn->id,
            'id_list' => 'required',
            'sort' => 'required'
        ];
    }
}
