<?php

namespace App\Models;

class IndexNode extends Model
{
    protected $table = 'index_node';

    protected $fillable = ['name', 'url', 'pid', 'method', 'active', 'sort'];

    /**
     * 缓存数据的来源
     *
     * @return array
     */
    protected static function cacheSource()
    {
        return static::orderBy('pid', 'asc')->orderBy('sort', 'asc')->get()->toArray();
    }
}
