<?php

namespace App\Http\Requests\Api\v1;

class CreateAdvertRequest extends Request
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
            'image' => 'sometimes|required',
            'goods_id' => 'required_if:identity,shop|numeric',
            'promoteinfo' => 'required_if:identity,promote',
            'start_at' => 'required|date',
            'end_at' => 'date|after:start_at',
        ];
    }
}
