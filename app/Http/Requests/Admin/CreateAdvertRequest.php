<?php

namespace App\Http\Requests\Admin;

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
            'name'=>'required',
            'image'=>'required',//todo:可能需要验证是否是一个合法的图片
            'url'=>'required|url',
            'time_type'=>'required|in:1,2',
            'started_at'=>'sometimes|before:end_at',
        ];
    }
}
