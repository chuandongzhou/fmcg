<?php

namespace App\Models;


class SalesmanCustomer extends Model
{
    protected $table = 'salesman_customer';

    protected $fillable = [
        'number',
        'name',
        'platform_id', //平台id
        'contact',
        'contact_information',
        'business_area',
        'business_address_lng',
        'business_address_lat',
        'shipping_address_lng',
        'shipping_address_lat',
        'display_fee',
        'salesman_id'
    ];

    /**
     * 关联业务员
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesman()
    {
        return $this->belongsTo('App\Models\Salesman');
    }
}
