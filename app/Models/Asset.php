<?php

namespace App\Models;

class Asset extends Model
{
    protected $table = 'asset';
    protected $fillable = [
        'shop_id',
        'name',
        'quantity',
        'unit',
        'condition',
        'remark',
        'status'
    ];

    protected $appends = [];

    protected $hidden = [];

    /**
     * 所属店铺
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }

    /**
     * 资产申请使用与审核
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assetReview()
    {
        return $this->hasMany('App\Models\AssetApply');
    }
}
