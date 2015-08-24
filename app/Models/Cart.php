<?php

namespace App\Models;

class Cart extends Model
{
    protected $table = 'cart';
    public $timestamps = false;
    protected $fillable = ['goods_id', 'num', 'user_id', 'carted_at'];

    /**
     * 用户表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
