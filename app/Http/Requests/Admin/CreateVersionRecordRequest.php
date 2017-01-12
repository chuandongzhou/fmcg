<?php

namespace App\Http\Requests\Admin;

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
            'version_name' => 'required',
            'version_no' => 'required',
            'type' => 'required',
            'content' => 'required',
            'download_url' => 'required|url'
        ];
    }
}
