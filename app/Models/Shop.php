<?php

namespace App\Models;

use App\Services\AddressService;
use App\Services\ImageUploadService;
use App\Services\ShopService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{

    use SoftDeletes;

    protected $table = 'shop';

    protected $timestamp = false;

    protected $fillable = [
        'logo',
        'name',
        'license',
        'business_license',
        'agency_contract',
        'images',
        'mobile_images',
        'contact_person',
        'contact_info',
        'introduction',
        'min_money',
        'province_id',
        'city_id',
        'district_id',
        'street_id',
        'area',
        'area_name',
        'address',
        'delivery_location',
        'user_id',
        'license_num',
        'spreading_code',
        'nickname',
        'x_lng',
        'y_lat'
    ];

    protected $appends = ['logo_url'];

    protected $hidden = ['images', 'logo', 'created_at', 'updated_at'];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::deleted(function ($model) {
            $model->user()->delete();
            $model->deliveryMans()->delete();
            $model->goods()->delete();
            $model->deliveryArea()->delete();
            $model->shopAddress()->delete();
            $model->adverts()->delete();
            $model->shopRecommendGoods()->delete();
            $model->shopHomeAdverts()->delete();
            $model->ShopSignature()->delete();
        });
        static::updated(function ($model) {
            (new UserService(true))->setShopDetail($model->user);
        });
    }

    /**
     * 用户表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 送货人列表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deliveryMans()
    {
        return $this->hasMany('App\Models\DeliveryMan');
    }

    /**
     * 商品表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goods()
    {
        return $this->hasMany('App\Models\Goods');
    }

    /**
     * 关联文件表
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function files()
    {
        return $this->morphMany('App\Models\File', 'fileable');
    }

    /**
     * logo
     *
     * @return mixed
     */
    public function logo()
    {
        return $this->morphOne('App\Models\File', 'fileable')->where('type', cons('shop.file_type.logo'));
    }

    /**
     * 营业执照
     *
     * @return mixed
     */

    public function license()
    {
        return $this->morphOne('App\Models\File', 'fileable')->where('type', cons('shop.file_type.license'));
    }

    /**
     * 经营许可照
     *
     * @return mixed
     */
    public function businessLicense()
    {
        return $this->morphOne('App\Models\File', 'fileable')->where('type', cons('shop.file_type.business_license'));
    }

    /**
     * 代理合同
     *
     * @return mixed
     */
    public function agencyContract()
    {
        return $this->morphOne('App\Models\File', 'fileable')->where('type', cons('shop.file_type.agency_contract'));
    }

    /**
     * 店铺图片
     *
     * @return mixed
     */
    public function images()
    {
        return $this->morphMany('App\Models\File', 'fileable')->where('type', cons('shop.file_type.images'));
    }

    /**
     * 送货区域
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function deliveryArea()
    {
        return $this->morphMany('App\Models\AddressData', 'addressable')->where('type',
            cons('shop.address_type.delivery_address'));
    }

    /**
     * 地址
     *
     * @return mixed
     */
    public function shopAddress()
    {
        return $this->morphOne('App\Models\AddressData', 'addressable')->where('type',
            cons('shop.address_type.shop_address'));
    }

    /**
     * 该店铺下完成的订单数量
     *
     * @return mixed
     */
    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }


    /**
     * 前面广告
     *
     * @return mixed
     */
    public function adverts()
    {
        return $this->hasMany('App\Models\Advert')->where(function ($query) {
            $query->where('type', cons('advert.type.shop'))->orWhere('type', cons('advert.type.promote'));
        });

    }

    /**
     * 关联推荐商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function recommendGoods()
    {
        return $this->belongsToMany('App\Models\Goods', 'shop_recommend_goods', 'shop_id',
            'goods_id')->withTrashed();
    }

    /**
     * 该店铺所有推荐商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopRecommendGoods()
    {
        return $this->hasMany('App\Models\ShopRecommendGoods');
    }

    /**
     * 首页广告
     *
     * @return mixed
     */
    public function shopHomeAdverts()
    {
        return $this->hasMany('App\Models\ShopHomeAdvert');

    }

    /**
     * 店招
     *
     * @return mixed
     */
    public function shopSignature()
    {
        return $this->hasOne('App\Models\ShopSignature');
    }

    /**
     * 关联推广人员
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function promoters()
    {
        return $this->belongsTo('App\Models\Promoter', 'spreading_code', 'spreading_code');
    }

    /**
     * 关联业务员
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesmen()
    {
        return $this->hasMany('App\Models\Salesman');
    }

    /**
     * 所有客户
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function salesmenCustomer()
    {
        return $this->hasManyThrough('App\Models\SalesmanCustomer', 'App\Models\Salesman');
    }

    /**
     * 对应客户
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function salesmanCustomer()
    {
        return $this->hasOne(SalesmanCustomer::class);
    }

    /**
     * 关联抵费商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mortgageGoods()
    {
        return $this->hasMany('App\Models\MortgageGoods');
    }

    /**
     * 关联优惠券
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function coupons()
    {
        return $this->hasMany('App\Models\Coupon');
    }

    /**
     * 获取热门商家
     *
     * @param $query
     */
    public function scopeOfHot($query)
    {
        //TODO: 热门商家排序规则修改
        return $query->orderBy('id', 'desc');
    }


    /**
     * 店铺名搜索
     *
     * @param $query
     * @param $name
     * @return mixed
     */
    public function scopeOfName($query, $name)
    {
        if ($name) {
            return $query->where('name', 'LIKE', '%' . $name . '%');
        }
    }

    /**
     * 最新的商家
     *
     * @param $query
     */
    public function scopeOfNew($query)
    {
        return $query->orderBy('id', 'desc');
    }

    /**
     * 过滤地址或配送区域
     *
     * @param $query
     * @param $data
     * @param string $relation
     */
    public function scopeOfDeliveryArea($query, $data, $relation = 'deliveryArea')
    {
        $data = array_only($data, ['province_id', 'city_id', 'district_id', 'street_id']);
        if (!empty($data)) {
            return $query->whereHas($relation, function ($query) use ($data) {
                $query->where(array_filter($data));
            });
        }
    }

    /**
     * search shop by User
     *
     * @param $query
     * @param $myType
     * @param $shopType
     * @return mixed
     */
    public function scopeOfUser($query, $myType, $shopType = 0)
    {
        return $query->whereHas('user', function ($q) use ($myType, $shopType) {
            $q->where('type', '>', $myType)->active()->where('audit_status',
                cons('user.audit_status.pass'));
            if ($shopType) {
                $q->where('type', $shopType);
            }
        });
    }

    /**
     * 按类型搜索
     *
     * @param $query
     * @param $userType
     * @return mixed
     */
    public function scopeOfUserType($query, $userType = null)
    {
        if ($userType) {
            return $query->whereHas('user', function ($q) use ($userType) {
                if ($userType) {
                    $q->where('type', $userType);
                }
            });
        }
    }

    /**
     * 设置logo
     *
     * @param $logo
     * @return bool
     */
    public function setLogoAttribute($logo)
    {
        if ($logo) {
            return $this->associateFile($this->convertToFile($logo), 'logo', cons('shop.file_type.logo'));
        }
    }

    /**
     * 设置营业执照
     *
     * @param $license
     * @return bool
     */
    public function setLicenseAttribute($license)
    {
        if ($license && !$this->license) {
            return $this->associateFile($this->convertToFile($license), 'license', cons('shop.file_type.license'));
        }
    }

    /**
     * 设置经营许可证
     *
     * @param $license
     * @return bool
     */
    public function setBusinessLicenseAttribute($license)
    {
        if ($license && !$this->businessLicense) {
            return $this->associateFile($this->convertToFile($license), 'businessLicense',
                cons('shop.file_type.business_license'));
        }
    }

    /**
     * 设置代理合同
     *
     * @param $agencyContract
     * @return bool
     */
    public function setAgencyContractAttribute($agencyContract)
    {
        if ($agencyContract && !$this->agencyContract) {
            return $this->associateFile($this->convertToFile($agencyContract), 'agencyContract',
                cons('shop.file_type.agency_contract'));
        }
    }

    /**
     *  设置店铺图片
     *
     * @param $images ['id'=>['1' ,''] , 'path'=>['','']]
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
            return $this->associateFiles($imagesArr, 'images', cons('shop.file_type.images'), false);
        }

        return true;
    }

    /**
     * 手机传上来的图片
     *
     * @param $images
     * @return bool
     */
    public function setMobileImagesAttribute($images)
    {

        if (!empty($images)) {
            return $this->associateFiles($images, 'files', cons('shop.file_type.images'), false);
        }

        return true;
    }


    /**
     * 设置店铺地址信息
     *
     * @param $address
     * @return bool
     */
    public function setAddressAttribute($address)
    {
        $relate = $this->shopAddress();
        $relate->delete();
        $address['type'] = cons('shop.address_type.shop_address');
        if ($this->exists) {
            $relate->create($address);
        } else {
            static::created(function ($model) use ($address) {
                $model->shopAddress()->create($address);
            });
        }
        return true;
    }

    /**
     * 配送区域
     *
     * @param $area
     * @return bool
     */
    public function setAreaAttribute($area)
    {
        $areaArr = (new AddressService($area))->formatAddressPost();
        if (!empty($areaArr)) {
            $this->deliveryArea()->delete();
            $areas = [];
            foreach ($areaArr as $data) {
                unset($data['coordinate']);
                $areas[] = new AddressData($data);
            }
            if ($this->exists) {
                $this->deliveryArea()->saveMany($areas);
            } else {
                static::created(function ($model) use ($areas) {
                    $model->deliveryArea()->saveMany($areas);
                });

            }
        }
        return true;
    }


    /**
     * 获取logo链接
     *
     * @return string
     */
    public function getLogoUrlAttribute()
    {
        $logo = $this->logo;
        $userType = array_flip(cons('user.type'));
        return $logo ? $logo->url : asset('images/' . $userType[$this->user->type] . '.jpg');
    }

    /**
     * 获取营业执照链接
     *
     * @return string
     */
    public function getLicenseUrlAttribute()
    {
        $license = $this->license;

        return $license ? $license->url : '';
    }

    /**
     * 获取经营许可证链接
     *
     * @return string
     */
    public function getBusinessLicenseUrlAttribute()
    {
        $businessLicense = $this->businessLicense;

        return $businessLicense ? $businessLicense->url : '';
    }

    /**
     * 获取代理合同链接
     *
     * @return string
     */
    public function getAgencyContractUrlAttribute()
    {
        $agencyContract = $this->agencyContract;

        return $agencyContract ? $agencyContract->url : '';
    }

    /**
     * 获取店铺图片
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        $image = $this->images->first();

        return $image ? $image->url : asset('images/shop_default.png');
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
            $result[$key]['name'] = $image['name'];
            $result[$key]['path'] = $image['path'];
            $result[$key]['url'] = $image->url;
        }
        return $result;
    }

    /**
     * 店铺二维码
     *
     * @return string
     */
    public function getQrcodeUrlAttribute()
    {
        return (new ShopService())->qrcode($this->id);
    }

    /**
     * 获取地址
     *
     * @return string
     */
    public function getAddressAttribute()
    {
        return is_null($this->shopAddress) ? '' : $this->shopAddress->address_name;
    }

    /**
     * 获取商家销量
     *
     * @return mixed
     */
    public function getSalesVolumeAttribute()
    {
        $num = $this->goods()->sum('sales_volume');
        $res = $num < 10000 ? $num : (floor($num / 100) / 100) . '万';
        return $res;
    }

    /**
     * 获取店家商品数量
     *
     */
    public function getGoodsCountAttribute()
    {
        return $this->goods->count();
    }

    /**
     * 获取店家三个商品 按id desc
     *
     */
    public function getThreeGoodsAttribute()
    {
        return $this->goods()->OfNew()->limit(3)->get();
    }


    /**
     * 获取userName
     *
     * @return int|mixed|string
     */
    public function getUserNameAttribute()
    {
        $shopService = new ShopService(true);
        if ($userName = $shopService->getUserDetail($this->id, 'name')) {
            return $userName;
        }
        return $shopService->setUserDetail($this, 'name');
    }

    /**
     * 获取userType
     *
     * @return int|mixed|string
     */
    public function getUserTypeAttribute()
    {
        $shopService = new ShopService(true);

        if ($userType = $shopService->getUserDetail($this->id, 'type')) {
            return $userType;
        }
        return $shopService->setUserDetail($this, 'type');
    }

    /**
     * 获取userType
     *
     * @return int|mixed|string
     */
    public function getUserBackupMobileAttribute()
    {
        $shopService = new ShopService(true);

        if ($userType = $shopService->getUserDetail($this->id, 'mobile')) {
            return $userType;
        }
        return $shopService->setUserDetail($this, 'mobile');
    }

}
