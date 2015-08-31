<?php

namespace App\Models;


class Attr extends Model
{
    protected $table = 'attr';
    public $timestamps = false;
    protected $fillable = ['name', 'category_id', 'pid', 'status', 'sort', 'is_default'];

    /**
     * 分类表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    /**
     * 关联商品表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function goods()
    {
        return $this->belongsToMany('App\Models\Goods', 'goods_attr');
    }
}
