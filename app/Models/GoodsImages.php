<?php

namespace App\Models;


class GoodsImages extends Model
{
    protected $table = 'goods_images';
    protected $fillable = [
        'level1',
        'level2',
        'level3',
        'attrs',
        'image'
    ];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::deleted(function ($image) {
            $image->image()->delete();
        });
    }

    /**
     * 关联文件表
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function image()
    {
        return $this->morphOne('App\Models\File', 'fileable');
    }

    public function setImageAttribute($image)
    {
        return $this->associateFile(upload_file($image, 'temp'), 'image');
    }
}
