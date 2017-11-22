<?php

namespace App\Http\Requests\Admin;


class CreateWeixinArticleRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'=>'required|max:40',
            'link_url'=>'required|url',
            'image' => 'sometimes|required'
        ];
    }
}
