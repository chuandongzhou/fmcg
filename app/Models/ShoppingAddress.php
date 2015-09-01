<?php

namespace App\Models;

class ShoppingAddress extends Model
{
    //
    protected $table = 'shopping_address';
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
