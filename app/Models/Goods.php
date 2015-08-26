<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/6
 * Time: 16:29
 */
namespace App\Models;


use App\Services\ImageUploadService;

class Goods extends Model
{
    protected $table = 'goods';
    protected $fillable = [
        'name',
        'price',
        'category_id',
        'brand_id',
        'packing',
        'is_new',
        'is_out',
        'is_change',
        'is_back',
        'is_expire',
        'is_promotion',
        'promotion_info',
        'min_num',
        'introduce',
        'shop_id'
    ];

    /**
     * 所属店铺
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App/shop');
    }

    /**
     * 配送区域
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goodsDeliveryArea()
    {
        return $this->hasMany('App\Models\GoodsDeliveryArea');
    }

    /**
     * 订单里的商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function orderGoods()
    {
        return $this->hasMany('App\Models\OrderGoods');
    }

    /**
     * 关联文件表
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images()
    {
        return $this->morphMany('App\Models\File', 'fileable');
    }

    /**
     * 查询热销产品
     *
     * @param $query
     */
    public function scopeHot($query)
    {
        $query->orderBy('sales_volume', 'desc');
    }

    /**
     * 查询新品
     *
     * @param $query
     */
    public function scopeNew($query)
    {
        $query->where('is_new', 1);
    }

    /**
     * 查询促销产品
     * @param $query
     */
    public function scopePromotion($query)
    {
        $query->where('is_promotion', 1);
    }

    /**
     *  设置店铺图片
     *
     * @param $images ['id'=>['1' ,''] , 'path'=>'']
     * @return bool
     */
    public function setImagesAttribute($images)
    {
        //格式化图片数组
        $imagesArr = (new ImageUploadService($images))->formatImagePost();
        //删除的图片
        $files = $this->files();
        if (!empty (array_filter($images['id']))) {
            $files = $files->whereNotIn('id', array_filter($images['id']));
        }
        $files->where('type', cons('shop.file_type.images'))->delete();

        if (!empty($imagesArr)) {
            return $this->associateFiles($imagesArr, 'images', 0, false);
        }
        return true;
    }

}