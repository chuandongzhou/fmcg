<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryMan extends Model
{
    use SoftDeletes;

    protected $table = 'delivery_man';
    protected $fillable = ['name', 'phone', 'shop_id'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * 店铺表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }
}
