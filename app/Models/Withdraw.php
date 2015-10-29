<?php

namespace App\Models;


class Withdraw extends Model
{
    protected $table = 'withdraw';
    protected $fillable = [
        'user_id',
        'user_bank_id',
        'amount',
        'status',
        'trade_no',
        'reason',
        'created_at',
        'failed_at',
        'pass_at',
        'payment_at'
    ];

    public $timestamps = false;
    public $dates = ['created_at', 'failed_at', 'pass_at', 'payment_at'];

    /**
     * 关联提现用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


    /**
     * 关联提现账号
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userBanks()
    {
        return $this->belongsTo('App\Models\UserBank', 'user_bank_id', 'id');
    }
}
