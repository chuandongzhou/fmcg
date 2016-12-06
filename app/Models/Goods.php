<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/6
 * Time: 16:29
 */
namespace App\Models;


use App\Services\GoodsImageService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Goods extends Model
{
    use SoftDeletes;

    protected $table = 'goods';
    protected $fillable = [
        'name',
        'price_retailer',
        'price_retailer_pick_up',
        'pieces_retailer',
        'min_num_retailer',
        'specification_retailer',
        'price_wholesaler',
        'price_wholesaler_pick_up',
        'pieces_wholesaler',
        'min_num_wholesaler',
        'specification_wholesaler',
        'shelf_life',
        'bar_code',
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
        'status',
        'user_type'
    ];

    public $appends = ['image_url', 'pieces', 'price'];

    protected $hidden = [
        'images',
        'created_at',
        'updated_at',
    ];
    protected $dates = ['deleted_at'];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        static::deleted(function ($model) {
            $model->deliveryArea()->delete();   //配送区域
            $model->carts()->delete();           //购物车
            $model->attr()->detach();           //商品标签
            //$model->images()->detach();         //商品图片
        });

//        static::creating(function ($model) {
//            $model->user_type = auth()->user()->type;
//
//        });
    }

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
     * recommendGoods推荐商品店铺
     * * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function recommendShop()
    {
        return $this->belongsToMany('App\Models\Shop', 'shop_recommend_goods', 'goods_id', 'shop_id');
    }

    /**
     * 商品单位表
     *
     */
    public function goodsPieces()
    {
        return $this->hasOne('App\Models\GoodsPieces');
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
        return $this->belongsToMany('App\Models\Attr', 'attr_goods', 'goods_id', 'attr_id')->where('category_id',
            $this->category_id)->withPivot('attr_pid');
    }

    /**
     * 关联文件表
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images()
    {
        return $this->hasMany('App\Models\Images', 'bar_code', 'bar_code')->where('status', 1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function deliveryArea()
    {
        return $this->morphMany('App\Models\AddressData', 'addressable');
    }

    /**
     * 关联抵费商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mortgageGoods()
    {
        return $this->hasOne('App\Models\MortgageGoods');
    }

    /**
     * 查询热销产品
     *
     * @param $query
     */
    public function scopeOfHot($query)
    {
        return $query->orderBy('sales_volume', 'desc');
    }

    /**
     * 通用排序
     *
     * @param $query
     * @return mixed
     */
    public function scopeOfCommonSort($query)
    {
        return $query->orderBy('is_promotion', 'desc')->orderBy('is_new', 'DESC')->orderBy('is_out', 'asc');
    }

    /**
     * 查询新品
     *
     * @param $query
     */
    public function scopeOfNew($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * 价格由低到高
     *
     * @param $query
     */
    public function scopeOfPrice($query, $shop_user_id = 0)
    {
        $typeName = (new UserService())->getUserTypeName();
        info($this->user_type);
        $typeName = ($typeName == 'supplier' ? 'wholesaler' : ($typeName == 'wholesaler' && auth()->id() == $shop_user_id ? 'retailer' : $typeName));
        return $query->orderBy('price_' . $typeName, 'asc');
    }

    /**
     * 名称排序
     *
     * @param $query
     */
    public function scopeOfName($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * 查询促销产品
     *
     * @param $query
     */
    public function scopeOfPromotion($query)
    {
        return $query->where('is_promotion', 1);
    }

    /**
     * 配送地址
     *
     * @param $query
     * @param $data
     */
    public function scopeOfDeliveryArea($query, $data)
    {
        $goodsBuilder = clone $query;
        $goodsIds = $goodsBuilder->lists('id');
        $data = array_only($data, ['province_id', 'city_id', 'district_id', 'street_id']);
        $goodsIds = DB::table('address_data')->where(array_filter($data))->where('addressable_type',
            "App\\Models\\Goods")->whereIn('addressable_id', $goodsIds)->lists('addressable_id');
        return $query->whereIn('id', $goodsIds);


        /* $data = array_only($data, ['province_id', 'city_id', 'district_id', 'street_id']);
         return $query->whereHas('deliveryArea', function ($query) use ($data) {
             $query->where($data);
         });*/
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
     *  按标签查询
     *
     * @param $query
     * @param $attr
     */
    public function scopeOfAttr($query, $attr)
    {
        $goodsAttr = DB::table('attr_goods')->select(DB::raw('goods_id ,count(attr_id) as num'))->whereIn('attr_id',
            (array)$attr)->groupBy('goods_id')->get();
        $goodsAttr = array_filter($goodsAttr, function ($item) use ($attr) {
            return $item->num == count($attr);
        });
        $goodsIds = array_pluck($goodsAttr, 'goods_id');


        return $query->whereIn('id', $goodsIds);
    }

    /**
     * 商品状态
     *
     * @param $query
     * @param $status
     * @return mixed
     */
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 未上线商品
     *
     * @param $query
     * @return mixed
     */
    public function scopeOfNotOn($query)
    {
        return $this->scopeOfStatus($query, 0);
    }


    /**
     * 设置终端商自提价格
     *
     * @param $priceRetailerPickUp
     */
    public function setPriceRetailerPickUpAttribute($priceRetailerPickUp)
    {
        if (!$priceRetailerPickUp || $priceRetailerPickUp <= 0) {
            $this->attributes['price_retailer_pick_up'] = $this->attributes['price_retailer'];
        } else {
            $this->attributes['price_retailer_pick_up'] = $priceRetailerPickUp;
        }
    }

    /**
     * 设置批发商自提价格
     *
     * @param $priceWholesalerPickUp
     */
    public function setPriceWholesalerPickUpAttribute($priceWholesalerPickUp)
    {
        if (!$priceWholesalerPickUp || $priceWholesalerPickUp <= 0) {
            $this->attributes['price_wholesaler_pick_up'] = $this->attributes['price_wholesaler'];
        } else {
            $this->attributes['price_wholesaler_pick_up'] = $priceWholesalerPickUp;
        }
    }

    /**
     * 根据不同角色获取价格
     *
     * @return mixed
     */
    public function getPriceAttribute()
    {
        $userType = auth()->user() ? auth()->user()->type : cons('user.type.retailer');

        return $userType == $this->user_type && $userType != cons('user.type.supplier') ? $this->price_retailer : ($userType == cons('user.type.wholesaler') || $userType == cons('user.type.supplier') ? $this->price_wholesaler : $this->price_retailer);
    }

    /**
     * 根据不同角色获取价格
     *
     * @return mixed
     */
    public function getPickUpPriceAttribute()
    {
        $user = auth()->user();
        $userType = $user ? $user->type : cons('user.type.retailer');

        return $userType == $this->user_type ? $this->price_retailer_pick_up : ($userType == cons('user.type.wholesaler') ? $this->price_wholesaler_pick_up : $this->price_retailer_pick_up);
    }

    /**
     * 根据不同角色获取单位
     *
     * @return mixed
     */
    public function getPiecesAttribute()
    {
        $piece = $this->pieces_id;
        return cons()->valueLang('goods.pieces', $piece);
    }

    /**
     * 根据不同角色获取单位Id
     *
     * @return mixed
     */
    public function getPiecesIdAttribute()
    {
        $userTypes = cons('user.type');

        $retailerPieces = $this->pieces_retailer;
        $wholesalerPieces = $this->pieces_wholesaler;

        $userType = auth()->user() ? auth()->user()->type : cons('user.type.retailer');
        $piece = $userType == $this->user_type ? ($userType == $userTypes['wholesaler'] ? $retailerPieces : $wholesalerPieces) : ($userTypes['wholesaler'] ? $wholesalerPieces : $retailerPieces);
        return $piece;
    }

    /**
     * 根据不同角色获取规格
     *
     * @return mixed
     */
    public function getSpecificationAttribute()
    {
        $userType = auth()->user()->type;
        $specification = $userType == $this->user_type ? $this->specification_retailer : ($userType == cons('user.type.wholesaler') ? $this->specification_wholesaler : $this->specification_retailer);
        return $specification;
    }

    /**
     * 根据不同角色获取最低购买数
     *
     * @return mixed
     */
    public function getMinNumAttribute()
    {
        $userType = auth()->user() ? auth()->user()->type : cons('user.type.retailer');

        return $userType == $this->user_type ? $this->min_num_retailer : ($userType == cons('user.type.wholesaler') ? $this->min_num_wholesaler : $this->min_num_retailer);

    }

    /**
     * 获取商品的单个图片地址，用于订单页面显示
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {

        $goodsService = new GoodsImageService();
        $goodsId = $this->id;
        if ($goodsService->hasImage($goodsId)) {

            return $goodsService->getImage($goodsId);
        }
        $image = $this->images->first();
        $url = $image ? $image->image_url : asset('images/goods_default.png');
        $goodsService->setImage($this->id, $url);
        return $url;
    }

    /**
     * 格式化图片地址
     */
    public function getImagesUrlAttribute()
    {
        $images = $this->images ? $this->images : [];

        $result = [];
        foreach ($images as $key => $image) {
            $result[$key]['id'] = $image['id'];
            $result[$key]['name'] = $image->image ? $image->image->name : 'goods_default';
            $result[$key]['url'] = $image->image_url;
        }
        return $result;
    }

    /**
     * 获取商品分类
     *
     * @return mixed
     */
    public function getCategoryIdAttribute()
    {
        return $this->cate_level_3 ? $this->cate_level_3 : ($this->cate_level_2 ? $this->cate_level_2 : $this->cate_level_1);
    }

    /**
     * 是否是抵费商品
     *
     * @return bool
     */
    public function getIsMortgageGoodsAttribute()
    {
        return !is_null($this->mortgageGoods);
    }
}