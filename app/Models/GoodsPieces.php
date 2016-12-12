<?php

namespace App\Models;

class GoodsPieces extends Model
{
    protected $table = 'goods_pieces';
    protected $fillable = [
        'pieces_level_1',
        'pieces_level_2',
        'pieces_level_3',
        'system_1',
        'system_2',
        'specification',
        'goods_id'
    ];
    public $timestamps = false;

    public function goods()
    {
        return $this->belongsTo('App\Models\Goods');
    }

}