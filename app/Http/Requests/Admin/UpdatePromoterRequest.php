<?php

namespace App\Http\Requests\Admin;


class UpdatePromoterRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $promoter = $this->route('promoter');
        return [
            'name' => 'required|unique:promoter,name,' . $promoter->id,
            'contact => required'
        ];
    }
}
