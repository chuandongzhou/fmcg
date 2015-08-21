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
            'name' => 'sometimes|required',
            'image' => 'sometimes|required',
            'url' => 'sometimes|required|url',
            'start_at' => 'sometimes|required|date',
            'end_at' => 'date|after:start_at',
        ];
    }
}
