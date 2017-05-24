<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class PromoGoods extends Model
{
    use SoftDeletes;
    protected $table = 'promo_goods';
    protected $fillable = [
        'shop_id',
        'goods_id',
        'status',
        'deleted_at',
    ];

    /**
     * 关联商品表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods');
    }

    /**
     * 名称或者二维码搜索
     * @param $query
     * @param $condition
     * @return mixed
     */
    public function scopeOfNameBarCode($query, $condition)
    {
        if ($condition) {
            return $query->whereHas('goods', function ($query) use ($condition) {
                $field = is_numeric($condition) ? 'bar_code' : 'name';
                $query->where($field, 'LIKE', '%' . $condition . '%');
            });
        }
    }
    
    /**
     * 名称或者二维码搜索
     * @param $query
     * @param $condition
     * @return mixed
     */
    public function scopeOfNotIds($query, $ids)
    {
        if ($ids) {
            return $query->whereHas('goods', function ($query) use ($ids) {
                $query->whereNotIn('id', $ids);
            });
        }
    }
    
}
