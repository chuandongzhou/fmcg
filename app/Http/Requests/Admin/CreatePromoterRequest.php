<?php

namespace App\Http\Requests\Admin;


class CreatePromoterRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:promoter',
            'contact' => 'required',
            'start_at' => 'required',
        ];
    }
}
