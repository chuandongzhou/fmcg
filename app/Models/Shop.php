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
     * 收藏表
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function likes()
    {
        return $this->morphMany('App\Models\Like', 'likeable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function deliveryArea()
    {
        return $this->morphMany('App\Models\DeliveryArea', 'addressable');
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
     * 配送地址
     *
     * @param $deliveryArea
     */
    public function setDeliveryAreaAttribute($deliveryArea)
    {
        if ($deliveryArea) {
            $this->attributes['delivery_area'] = implode(',', $deliveryArea);
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
        dd($license);
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
     * 配送区域
     *
     * @param $area
     * @return bool
     */
    public function setAreaAttribute($area)
    {
        $areaArr = (new AddressService($area))->formatAddressPost();

        /* $nowArea = $this->deliveryArea;
       * if (count($nowArea) == count(array_filter($area['province_id']))) {
             return true;
         }*/
        $this->deliveryArea()->delete();
        if (!empty($areaArr)) {
            foreach ($areaArr as $data) {
                $this->deliveryArea()->create($data);
            }
        }
        return true;
    }


}
