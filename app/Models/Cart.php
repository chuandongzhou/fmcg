<?php

namespace App\Models;


use Carbon\Carbon;

class Cart extends Model
{
    protected $table = 'cart';
    public $timestamps = false;
    protected $fillable = ['goods_id', 'num', 'user_id', 'carted_at'];
    public $dates = ['carted_at'];

    public static function boot(){
        parent::boot();
        static::creating(function($model){
            $model->carted_at = Carbon::now();
        });
    }


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
     * 商品
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods(){
        return $this->belongsTo('App\Models\Goods');
    }
}
