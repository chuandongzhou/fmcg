<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsDeliveryArea extends Model
{
    protected $table = 'goodsDeliveryArea';
    public $timestamp = false;

    /**
     * ��Ʒ��
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo('app/Goods');
    }
}
