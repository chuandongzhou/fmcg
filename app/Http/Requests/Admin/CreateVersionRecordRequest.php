<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class CreateVersionRecordRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'version_name' => 'required|unique',
            'version_no' => 'required',
            'type' => 'required|numeric',
            'content' => 'required'
        ];
    }
}
