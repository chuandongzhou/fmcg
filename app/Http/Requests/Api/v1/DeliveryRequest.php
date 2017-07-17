<?php
namespace App\Http\Requests\Api\v1;



class DeliveryRequest extends Request{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_at' => 'sometimes|required|date',
            'end_at' => 'sometimes|required|date',
            'shop_name' => 'sometimes|string',
            'order_id' => 'sometimes|integer',
        ];
    }
}