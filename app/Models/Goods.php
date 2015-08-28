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
        'cate_level_1',
        'cate_level_2',
        'cate_level_3',
        'is_new',
        'is_out',
        'is_change',
        'is_back',
        'is_expire',
        'is_promotion',
        'promotion_info',
        'min_num',
        'introduce',
        'shop_id',
        'images',
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
     * 订单里的商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function orderGoods()
    {
        return $this->hasMany('App\Models\OrderGoods');
    }

    /**
     * 关联标签表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attr()
    {
        return $this->belongsToMany('App\Models\Attr')->withPivot('attr_pid');
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function deliveryArea()
    {
        return $this->morphMany('App\Models\Address', 'addressable');
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
     *
     * @param $query
     */
    public function scopePromotion($query)
    {
        $query->where('is_promotion', 1);
    }

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::deleted(function ($model) {
            // 删除所有关联文件
           $model->deliveryArea->delete();
        });
    }

    /**
     *  设置图片
     *
     * @param $images ['id'=>['1' ,''] , 'path'=>'']
     * @return bool
     */
    public function setImagesAttribute($images)
    {
        //格式化图片数组
        $imagesArr = (new ImageUploadService($images))->formatImagePost();
        //删除的图片
        $files = $this->images();
        if (!empty (array_filter($images['id']))) {
            $files = $files->whereNotIn('id', array_filter($images['id']));
        }
        $files->delete();

        if (!empty($imagesArr)) {
            return $this->associateFiles($imagesArr, 'images', 0, false);
        }
        return true;
    }

}