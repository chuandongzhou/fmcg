<?php

namespace App\Http\Requests\Admin;

class UpdateAdvertRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category_id' => 'sometimes|required|exists:category,id',
            'name' => 'sometimes|required',
            'image' => 'sometimes|required',
            'url' => 'sometimes|required|url',
            'sort' => 'required|integer',
            'start_at' => 'sometimes|required|date',
            'end_at' => 'date|after:start_at',
        ];
    }
}
