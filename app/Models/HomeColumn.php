<?php

namespace App\Models;


class HomeColumn extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'home_column';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'type', 'id_list', 'sort'];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * 获取id列表
     *
     * @return array
     */
    public function getIdArrayAttribute($idList)
    {
        return $idList ? explode('|', $idList) : '';
    }
}
