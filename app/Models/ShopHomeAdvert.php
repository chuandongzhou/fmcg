<?php

namespace App\Models;



class ShopHomeAdvert extends Model
{
    protected $table = 'shop_home_advert';
    protected $fillable = [
        'image',
        'url',
        'status',
        ];
    protected $appends = ['image_url'];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::deleted(function ($model) {
            $model->image()->delete();

        });
    }
    /**
     * 关联广告图片文件
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function image()
    {
        return $this->morphOne('App\Models\File', 'fileable');
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
     * 获取图片链接
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        $image = $this->image;

        return $image ? upload_file_url($image->path) : '';
    }



}