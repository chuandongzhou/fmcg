<?php
namespace App\Http\Requests\Api\v1;



class DeliveryRequest extends Request{
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
        ];
    }
}