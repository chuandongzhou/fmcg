<?php

namespace App\Models;


use Carbon\Carbon;

class Advert extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'advert';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_id',
        'goods_id',
        'province_id',
        'city_id',
        'category_id',
        'name',
        'type',
        'url',
        'start_at',
        'end_at',
        'image'
    ];

    public $hidden = ['image', 'type', 'start_at', 'end_at', 'created_at', 'updated_at'];

    public $appends = ['image_url'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['start_at', 'end_at'];

    /**
     * 关联广告图片文件
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function image()
    {
        return $this->morphOne('App\Models\File', 'fileable');
    }

    public function scopeOfTime($query, $nowTime = null, $limit = 4)
    {
        $nowTime = $nowTime ?: Carbon::now();
        return $query->where('start_at', '<', $nowTime)->Where(function ($query) use ($nowTime) {
            $query->where('end_at', '>', $nowTime)->orWhere('end_at', null);
        })->orderBy('id', 'asc')->take($limit);
    }


    /**
     * 过滤地址或配送区域
     *
     * @param $query
     * @param $data
     */
    public function scopeOfAddress($query, $data)
    {
        if (isset($data['province_id']) && isset($data['city_id'])) {
            return $query->where(['province_id' => $data['province_id'], 'city_id' => $data['city_id']]);
        } elseif (isset($data['province_id'])) {
            return $query->where('province_id', $data['province_id']);
        }
    }

    /**
     * 模型关联一个文件
     *
     * @param $image
     * @return bool
     */
    public function setImageAttribute($image)
    {
        return $this->associateFile($this->convertToFile($image), 'image');
    }

    /**
     * 设置链接地址
     *
     * @param $goodsId
     */
//    public function setGoodsIdAttribute($goodsId)
//    {
//        $this->attributes['url'] = url('goods/' . $goodsId);
//    }

    /**
     * 设置结束时间
     *
     * @param $endAt
     */
    public function setEndAtAttribute($endAt)
    {
        if (!$endAt || $endAt == "") {
            $this->attributes['end_at'] = null;
        } else {
            $this->attributes['end_at'] = $endAt;
        }
    }

    /**
     * 获取图片链接
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        $image = $this->image;

        return $image ? upload_file_url($image->path) : '';
    }

    /**
     * 获取商品id
     *
     * @return int
     */
    public function getGoodsIdAttribute()
    {
        $url = $this->url;
        preg_match_all('/goods\/(\d+)/', $url, $matched);

        return empty($matched[1]) ? 0 : $matched[1][0];
    }

    /**
     * 获取当前广告状态
     *
     * @return string
     */
    public function getStatusNameAttribute()
    {
        if ($this->start_at->isFuture()) {
            return '未开始';
        }

        $endAt = $this->end_at;
        if (is_null($endAt)) {
            return '永久';
        }

        if ($endAt->isPast()) {
            return '已结束';
        }

        return '展示中';
    }
}
