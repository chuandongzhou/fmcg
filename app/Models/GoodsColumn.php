<?php

namespace App\Models;


class GoodsColumn extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'goods_column';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['cate_level_1', 'id_list', 'province_id', 'city_id'];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * 获取id列表
     *
     * @return array
     */
    public function getIdListAttribute($idList)
    {
        return $idList ? explode('|', $idList) : '';
    }

    /**
     * 过滤地址或配送区域
     *
     * @param $query
     * @param $data
     */
    public function scopeOfAddress($query, $data)
    {
        if (isset($data['province_id']) && isset($data['city_id'])) {
            return $query->where(['province_id' => $data['province_id'], 'city_id' => $data['city_id']]);
        } elseif (isset($data['province_id'])) {
            return $query->where('province_id', $data['province_id']);
        }
    }
}
