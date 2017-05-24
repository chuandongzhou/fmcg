<?php

namespace App\Http\Requests\Api\v1;


use Carbon\Carbon;

class UpdateAssetApplyRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //$now = Carbon::now();
        return [
            'quantity' => 'sometimes|required|numeric|min:1000',
            'apply_remark' => 'sometimes|max:50',
            'use_date' => 'sometimes|date'//|after:'. $now,
        ];
    }
}
