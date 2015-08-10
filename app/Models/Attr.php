<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attr extends Model
{
    protected $table = 'attr';
    public $timestamp = false;
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
}
