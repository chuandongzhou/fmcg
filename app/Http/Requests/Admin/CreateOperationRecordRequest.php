<?php

namespace App\Http\Requests\Admin;


class CreateOperationRecordRequest extends Request
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
            'reason' => 'required',
            'content' => 'required',
            'start_at' =>'required|date',
            'end_at' => 'required|date|after:started_at'
        ];
    }
}
