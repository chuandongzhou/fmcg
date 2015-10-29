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
        'nickname',
        'x_lng',
        'y_lat'
    ];

    protected $appends = ['images_url','orders', 'logo_url'];

    protected $hidden = ['images', 'created_at', 'updated_at'];

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
        if ($license) {
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
        if ($license) {
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
        if ($agencyContract) {
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
            static::created(function ($model) use ($relate, $address) {
                $model->$relate()->save($address);
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
        $this->deliveryArea->each(function ($address) {
            $address->delete();
        });
        if (!empty($areaArr)) {
            foreach ($areaArr as $data) {
                if (isset($data['coordinate'])) {
                    $coordinate = $data['coordinate'];
                    unset($data['coordinate']);
                }
                $areas = new DeliveryArea($data);
                $areaModel = $this->deliveryArea()->save($areas);
                if (isset($coordinate)) {
                    $areaModel->coordinate()->create($coordinate);
                }
            }
        }
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
     * 获取经营许可证链接
     *
     * @return string
     */
    public function getBusinessLicenseUrlAttribute()
    {
        $businessLicense = $this->businessLicense;

        return $businessLicense ? upload_file_url($businessLicense->path) : '';
    }

    /**
     * 获取代理合同链接
     *
     * @return string
     */
    public function getAgencyContractUrlAttribute()
    {
        $agencyContract = $this->agencyContract;

        return $agencyContract ? upload_file_url($agencyContract->path) : '';
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
     * 格式化图片地址
     */
    public function getImagesUrlAttribute()
    {
        $images = $this->images ? $this->images : [];
        $result = [];
        foreach ($images as $key => $image) {
            $result[$key]['name'] = $image['name'];
            $result[$key]['path'] = $image->url;
        }
        return $result;
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

}
