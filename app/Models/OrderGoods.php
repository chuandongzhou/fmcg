<?php

namespace App\Models;


use App\Services\GoodsService;

class OrderGoods extends Model
{
    protected $table = 'order_goods';
    protected $fillable = [
        'goods_id',
        'type',
        'price',
        'num',
        'total_price',
        'pieces',
        'order_id',
    ];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::created(function ($model) {
            Goods::where('id', $model->goods_id)->increment('sales_volume', $model->num);
        });
    }

    /**
     * 订单表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    /**
     * 商品表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods')->withTrashed();
    }

    /**
     * 获取单位名
     *
     * @return string
     */
    public function getPiecesNameAttribute()
    {
        return cons()->valueLang('goods.pieces', $this->pieces);
    }

    /**
     * 商品名
     *
     * @return string
     */
    public function getGoodsNameAttribute()
    {
        return $this->goods ? $this->goods->name : '';
    }

    /**
     * 获取商品图片
     *
     * @return string
     */
    public function getImageAttribute()
    {
        return $this->goods ? $this->goods->image_url : asset('images/goods_default.png');
    }

    /**
     * 获取转换后的数量
     * @return mixed
     */
    public function getQuantityAttribute()
    {
      return $this->num * GoodsService::getPiecesSystem($this->goods_id,$this->pieces);
    }
    
}
