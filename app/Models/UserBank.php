<?php

namespace App\Models;

class UserBank extends Model
{
    protected $table = 'user_bank';
    protected $fillable = [
        'card_number',
        'card_type',
        'card_holder',
        'card_address',
        'is_default',
        'user_id'
    ];

    /**
     * 用户表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 提现订单
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function withdraws()
    {
        return $this->hasMany('App\Models\Withdraw', 'user_bank_id');
    }
}
