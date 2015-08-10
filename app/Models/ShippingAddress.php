<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    //
    protected $table = 'receivingAddress';
    protected $fillable = [
        'address',
        'consigner',
        'phone',
        'is_default',
        'user_id'
    ];

    /**
     * 用户表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
