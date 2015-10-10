<?php

namespace App\Models;

use App\Services\AddressService;
use App\Services\ImageUploadService;

class Shop extends Model
{
    protected $table = 'shop';
    protected $timestamp = false;
    protected $fillable = [
        'logo',
        'name',
        'license',
        'images',
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
        'nickname'
    ];

    protected $appends = ['image_url', 'orders'];

    protected $hidden = ['created_at' , 'updated_at'];

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
        return $this->morphMany('App\Models\DeliveryArea', 'addressable')->where('type',
            cons('shop.address_type.delivery_address'));
    }

    /**
     * 地址
     *
     * @return mixed
     */
    public function shopAddress()
    {
        return $this->morphOne('App\Models\DeliveryArea', 'addressable')->where('type',
            cons('shop.address_type.shop_address'));
    }

    /**
     * 该店铺下完成的订单数量
     *
     * @return mixed
     */
    public function orders()
    {
        return $this->hasMany('App\Models\Order')->where('status', cons('order.status.finished'))->count();
    }

    /**
     * 追加订单完成量
     *
     * @return mixed
     */
    public function getOrdersAttribute()
    {
        return $this->orders();
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
     * 最新的商家
     *
     * @param $query
     */
    public function scopeOfNew($query)
    {
        return $query->orderBy('id', 'desc');
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
     * 设置logo
     *
     * @param $logo
     * @return bool
     */
    public function setLogoAttribute($logo)
    {
        return $this->associateFiles((array)upload_file($logo, 'temp'), 'files', cons('shop.file_type.logo'));
    }

    /**
     * 设置营业执照
     *
     * @param $license
     * @return bool
     */
    public function setLicenseAttribute($license)
    {
        if ($license) {
            return $this->associateFiles((array)upload_file($license, 'temp'), 'files', cons('shop.file_type.license'));
        }
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
            return $this->associateFiles($imagesArr, 'files', cons('shop.file_type.images'), false);
        }

        return true;
    }

    /**
     * @param $address
     * @return bool
     */
    public function setAddressAttribute($address)
    {

        $this->shopAddress()->delete();
        $address['type'] = cons('shop.address_type.shop_address');
        $this->shopAddress()->create($address);

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
        $this->deliveryArea()->delete();
        if (!empty($areaArr)) {
            $areas = [];
            foreach ($areaArr as $data) {
                $areas[] = new DeliveryArea($data);
            }
            $this->deliveryArea()->saveMany($areas);
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

        return $logo ? upload_file_url($logo->path) : asset('images/u8.png');
    }

    /**
     * 获取营业执照链接
     *
     * @return string
     */
    public function getLicenseUrlAttribute()
    {
        $license = $this->license;

        return $license ? upload_file_url($license->path) : '';
    }

    /**
     * 获取店铺图片
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        $image = $this->images->first();

        return $image ? upload_file_url($image->path) : '';
    }

    /**
     * 获取地址
     *
     * @return string
     */
    public function getAddressAttribute()
    {
        return is_null($this->shopAddress) ? '' : $this->shopAddress->area_name . ' ' . $this->shopAddress->address;
    }

}
