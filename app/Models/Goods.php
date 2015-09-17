<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/6
 * Time: 16:29
 */
namespace App\Models;


use App\Services\ImageUploadService;
use DB;
use Auth;

class Goods extends Model
{
    protected $table = 'goods';
    protected $fillable = [
        'name',
        'price_retailer',
        'min_num_retailer',
        'price_wholesaler',
        'min_num_wholesaler',
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
        'introduce',
        'shop_id',
        'images',
    ];

    public $appends = ['image_url'];

    /**
     * 所属店铺
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
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
     * 关联订单
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function orders()
    {
        return $this->belongsToMany('App\Models\Order', 'order_goods', 'goods_id', 'order_id');

    }

    /**
     * 购物车内的商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts()
    {
        return $this->hasMany('App\Models\Cart');
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
        return $this->morphMany('App\Models\DeliveryArea', 'addressable');
    }

    /**
     * 查询热销产品
     *
     * @param $query
     */
    public function scopeHot($query)
    {
        return $query->orderBy('sales_volume', 'desc');
    }

    /**
     * 查询新品
     *
     * @param $query
     */
    public function scopeNew($query)
    {
        return $query->where('is_new', 1);
    }

    /**
     * 查询促销产品
     *
     * @param $query
     */
    public function scopePromotion($query)
    {
        return $query->where('is_promotion', 1);
    }

    /**
     * 价格由低到高
     *
     * @param $query
     */
    public function scopeOrderPrice($query)
    {
        return $query->orderBy('price_retailer', 'asc');
    }

    /**
     * 查询最新
     *
     * @param $query
     */
    public function scopeOrderNew($query)
    {
        return $query->orderBy('id', 'desc');
    }

    /**
     * 名称排序
     *
     * @param $query
     */
    public function scopeOrderName($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * 配送地址
     *
     * @param $query
     * @param $data
     */

    public function scopeOfDeliveryArea($query, $data)
    {
        if (isset($data['province_id'])
            && isset($data['city_id'])
            && isset($data['district_id'])
            && isset($data['street_id'])
        ) {
            $query->whereHas('deliveryArea', function ($query) use ($data) {
                $query->where([
                    'province_id' => $data['province_id'],
                    'city_id' => $data['city_id'],
                    'district_id' => $data['district_id'],
                    'street_id' => $data['street_id'],
                ]);
            });
        } elseif (isset($data['province_id']) && isset($data['city_id']) && isset($data['district_id'])) {
            $query->whereHas('deliveryArea', function ($query) use ($data) {
                $query->where([
                    'province_id' => $data['province_id'],
                    'city_id' => $data['city_id'],
                    'district_id' => $data['district_id']
                ]);
            });
        } elseif (isset($data['province_id']) && isset($data['city_id'])) {
            $query->whereHas('deliveryArea', function ($query) use ($data) {
                $query->where([
                    'province_id' => $data['province_id'],
                    'city_id' => $data['city_id']
                ]);
            });
        } elseif (isset($data['province_id'])) {
            $query->whereHas('deliveryArea', function ($query) use ($data) {
                $query->where([
                    'province_id' => $data['province_id']
                ]);
            });
        }
    }

    /**
     * 过滤分类
     *
     * @param $query
     * @param $categoryId
     * @param int $level
     */
    public function scopeOfCategory($query, $categoryId, $level = 1)
    {
        return $query->where('cate_level_' . $level, $categoryId);
    }

    /**
     * @param $query
     * @param $attr
     */
    public function scopeOfAttr($query, $attr)
    {
        $goodsAttr = DB::table('attr_goods')->select(DB::raw('goods_id ,count(attr_id) as num'))->whereIn('attr_id',
            $attr)->groupBy('goods_id')->get();

        $goodsAttr = array_filter($goodsAttr, function ($item) use ($attr) {
            return $item->num == count($attr);
        });
        $goodsIds = array_pluck($goodsAttr, 'goods_id');

        return $query->whereIn('id', $goodsIds);
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

        static::creating(function ($model) {
            $model->user_type = Auth::User()->type;
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

    /**
     * 根据不同角色获取价格
     *
     * @return mixed
     */
    public function getPriceAttribute()
    {
        return auth()->user()->type == cons('user.type.wholesaler') ? $this->price_wholesaler : $this->price_retailer;
    }

    /**
     * 根据不同角色获取最低购买数
     *
     * @return mixed
     */
    public function getMinNumAttribute()
    {
        return auth()->user()->type
        == cons('user.type.wholesaler') ? $this->min_num_wholesaler : $this->min_num_retailer;
    }

    /**
     * 获取商品的单个图片地址，用于订单页面显示
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        $image = $this->images->first();

        return $image ? upload_file_url($image->path) : '';
    }
}