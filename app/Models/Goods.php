<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/6
 * Time: 16:29
 */

namespace App\Models;


use App\Services\CategoryService;
use App\Services\GoodsImageService;
use App\Services\GoodsService;
use App\Services\InventoryService;
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
        'max_num_retailer',
        'specification_retailer',
        'price_wholesaler',
        'price_wholesaler_pick_up',
        'pieces_wholesaler',
        'min_num_wholesaler',
        'max_num_wholesaler',
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
        'is_gift',
        'promotion_info',
        'introduce',
        'warning_value',
        'warning_piece',
        'shop_id',
        'images',
        'status',
        'user_type'
    ];

    public $appends = ['image_url'];

    protected $hidden = [
        'images',
        'created_at',

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
            PromoGoods::where('goods_id', $model->id)->delete();//促销商品
        });

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

    public function inventory()
    {
        return $this->hasMany('App\Models\Inventory');
    }

    /**
     * 商品用户收藏
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function goodsLike()
    {
        return $this->belongsToMany('App\Models\User', 'like_goods', 'goods_id', 'user_id');
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
     * 关联促销商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function promoGoods()
    {
        return $this->hasOne('App\Models\PromoGoods');
    }

    /**
     * search shop by User
     *
     * @param $query
     * @return mixed
     */
    public function scopeShopUser($query)
    {
        /*return $query->whereHas('shop.user', function ($q) {
            $nowTime = Carbon::now();
            $q->active()->where('deposit', '>', 0)->where('expire_at', '>',
                $nowTime)->where('audit_status', cons('user.audit_status.pass'));
        });*/
    }

    /**
     * 搜索价格大于零
     *
     * @param $query
     * @param int $shopUserId
     * @return mixed
     */
    public function scopeHasPrice($query, $shopUserId = 0)
    {
        $typeName = (new UserService())->getUserTypeName();
        $typeName = ($typeName == 'supplier' || $typeName == 'maker' ? 'wholesaler' : ($typeName == 'wholesaler' && auth()->id() == $shopUserId ? 'retailer' : $typeName));

        return $query->where('price_' . $typeName, '>', 0);
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
    public function scopeOfPrice($query, $shopUserId = 0)
    {
        $typeName = (new UserService())->getUserTypeName();
        $typeName = ($typeName == 'supplier' ? 'wholesaler' : ($typeName == 'wholesaler' && auth()->id() == $shopUserId ? 'retailer' : $typeName));
        return $query->orderBy('price_' . $typeName, 'asc');
    }

    /**
     * 价格由高到低
     *
     * @param $query
     */
    public function scopeOfPriceDesc($query, $shopUserId = 0)
    {
        $typeName = (new UserService())->getUserTypeName();
        $typeName = ($typeName == 'supplier' ? 'wholesaler' : ($typeName == 'wholesaler' && auth()->id() == $shopUserId ? 'retailer' : $typeName));
        return $query->orderBy('price_' . $typeName, 'desc');
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
     * 按名称搜索
     *
     * @param $query
     * @param $name
     * @return mixed
     */
    public function scopeOfGoodsName($query, $name)
    {
        if ($name) {
            return $query->where('name', 'like', '%' . $name . '%');
        }
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
        if (!empty($data)) {
            $goodsIds = DB::table('address_data')->where(array_filter($data))->where('addressable_type',
                "App\\Models\\Goods")->whereIn('addressable_id', $goodsIds)->lists('addressable_id');
            return $query->whereIn('id', $goodsIds);
        }

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
     * 以名称或条形码检索
     *
     * @param $query
     * @param $nameOrCode
     * @return mixed
     */
    public function scopeOfNameOrCode($query, $nameOrCode)
    {
        if ($nameOrCode) {
            return $query->where(function ($query) use ($nameOrCode) {


                $query->where('name', 'LIKE', '%' . $nameOrCode . '%');
                $query->orWhere('bar_code', 'LIKE', '%' . $nameOrCode . '%');
            });
        }
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
        if (!is_null($status)) {
            return $query->where('status', $status);
        }
    }

    /**
     * 是否赠品
     *
     * @param $query
     * @param $gift
     * @return mixed
     */
    public function scopeOfGift($query, $gift)
    {
        if (!is_null($gift)) {

            return $query->where('is_gift', $gift);
        }
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
     * 关联商品单位
     *
     * @param $query
     * @param array $fields
     * @return mixed
     */
    public function scopeWithGoodsPieces(
        $query,
        $fields = ['goods_id', 'system_1', 'system_2', 'pieces_level_1', 'pieces_level_2', 'pieces_level_3']
    ) {
        return $query->with([
            'goodsPieces' => function ($query) use ($fields) {
                $query->select($fields);
            }
        ]);
    }

    /**
     * 按类型搜索
     *
     * @param $query
     * @param int $userType
     */
    public function scopeOfUserType($query, $userType = 2)
    {
        if ($userType) {
            $query->where('user_type', $userType);
        }
    }

    /**
     * 可购买的商品
     *
     * @param $query
     * @param int $type
     */
    public function scopeOfSearchType($query, $type = 2)
    {
        if ($type) {
            if ($type == cons('user.type.supplier')) {
                $query->where('user_type', cons('user.type.maker'));
            } else {
                $query->whereBetween('user_type', [$type + 1, cons('user.type.supplier')]);
            }
        }
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

        return $userType != $this->user_type && ($userType == cons('user.type.wholesaler') || $userType == cons('user.type.supplier')) ? $this->price_wholesaler : $this->price_retailer;
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

        return $userType != $this->user_type && ($userType == cons('user.type.wholesaler') || $userType == cons('user.type.supplier')) ? $this->price_wholesaler_pick_up : $this->price_retailer_pick_up;
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
        $retailerPieces = $this->pieces_retailer;
        $wholesalerPieces = $this->pieces_wholesaler;
        $userType = auth()->check() ? auth()->user()->type : cons('user.type.retailer');
        return $userType != $this->user_type && ($userType == cons('user.type.wholesaler') || $userType == cons('user.type.supplier')) ? $wholesalerPieces : $retailerPieces;
    }

    /**
     * 获取单位
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPiecesListAttribute()
    {
        $pieces = $this->goodsPieces;
        $items = ['pieces_level_1', 'pieces_level_2', 'pieces_level_3'];
        $piecesList = collect([]);
        foreach ($items as $item) {
            if (!is_null($piecesId = $pieces->$item)) {
                $piecesList->push($piecesId);
            }
        }
        return $piecesList;
    }

    /**
     * 根据不同角色获取规格
     *
     * @return mixed
     */
    public function getSpecificationAttribute()
    {
        $userType = auth()->user()->type;
        return $userType != $this->user_type && ($userType == cons('user.type.wholesaler') || $userType == cons('user.type.supplier')) ? $this->specification_wholesaler : $this->specification_retailer;
    }

    /**
     * 根据不同角色获取规格
     *
     * @return mixed
     */
    public function getSpecificationForChildAttribute()
    {
        $userType = child_auth()->user()->type;
        $specification = $userType != $this->user_type && ($userType == cons('user.type.wholesaler') || $userType == cons('user.type.supplier')) ? $this->specification_wholesaler : $this->specification_retailer;
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

        return $userType != $this->user_type && ($userType == cons('user.type.wholesaler') || $userType == cons('user.type.supplier')) ? $this->min_num_wholesaler : $this->min_num_retailer;

    }

    /**
     * 根据不同角色获取最低购买数
     *
     * @return mixed
     */
    public function getMaxNumAttribute()
    {
        $userType = auth()->check() ? auth()->user()->type : cons('user.type.retailer');

        $maxNum = $userType != $this->user_type && ($userType == cons('user.type.wholesaler') || $userType == cons('user.type.supplier')) ? $this->max_num_wholesaler : $this->max_num_retailer;

        return $maxNum ?: 20000;
    }

    /**
     * 获取商品的单个图片地址
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
        $url = asset('images/goods_default.png');
        if ($image) {
            $url = $image->image_url;
            $goodsService->setImage($this->id, $url);
        }
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
     * 获取商品分类ID
     *
     * @return mixed
     */
    public function getCategoryIdAttribute()
    {
        return $this->cate_level_3 ? $this->cate_level_3 : ($this->cate_level_2 ? $this->cate_level_2 : $this->cate_level_1);
    }

    /**
     * 获取商品分类名
     *
     * @return string
     */
    public function getCategoryNameAttribute()
    {
        $categorys = CategoryService::getCategories();

        $categoryName = '';
        if ($level1 = $this->cate_level_1) {
            $categoryName .= $categorys[$level1]['name'];
        }
        if ($level2 = $this->cate_level_2) {
            $categoryName .= '/' . $categorys[$level2]['name'];
        }
        if ($level3 = $this->cate_level_3) {
            $categoryName .= '/' . $categorys[$level3]['name'];
        }

        return $categoryName;
    }

    /**
     * 获取店铺名
     *
     * @return string
     */
    public function getShopNameAttribute()
    {
        return $this->shop ? $this->shop->name : '- -';
    }

    /**
     * 获取商品所属店铺用户类型
     *
     * @return string
     */
    public function getShopUserTypeAttribute()
    {
        return $this->shop ? $this->shop->user_type : '0';
    }


    /**
     * 获取是否收藏
     *
     * @return bool
     */
    public function getIsLikeAttribute()
    {
        if (auth()->check()) {
            return auth()->user()->likeGoods()->where('goods_id', $this->id)->pluck('id') ? true : false;
        }

        return false;
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

    /**
     * 是否是促销商品
     *
     * @return bool
     */
    public function getIsPromoGoodsAttribute()
    {
        return !is_null($this->promoGoods);
    }

    /**
     * 获得商品收藏次数
     *
     * @return mixed
     */
    public function getLikeAmountAttribute()
    {
        return $this->goodsLike->count();
    }

    /**
     * 获得商品库存剩余数量带单位字符串
     *
     * @return mixed
     */
    public function getSurplusInventoryAttribute()
    {
        return InventoryService::calculateQuantity($this, $this->getTotalInventoryAttribute());
    }

    /**
     * 获取商品剩余库存
     *
     * @return mixed
     */
    public function getTotalInventoryAttribute()
    {
        return $this->inventory->where('action_type', cons('inventory.action_type.in'))->sum('surplus') ?? 0;
    }

    /**
     * 库存预警
     *
     * @return bool
     */
    public function getNeedWarningAttribute()
    {
        $warning = $this->warning_value * GoodsService::getPiecesSystem($this,
                $this->warning_piece);
        $surplus = $this->getTotalInventoryAttribute();
        return intval($surplus) < intval($warning);
    }
}