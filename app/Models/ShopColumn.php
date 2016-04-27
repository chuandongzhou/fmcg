<?php

namespace App\Models;


class ShopColumn extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_column';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'id_list', 'sort'];

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
}
