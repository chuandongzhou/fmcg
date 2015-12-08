<?php

namespace App\Models;

class Images extends Model
{
    protected $table = 'images';
    protected $fillable = [
        'bar_code'
    ];
    public $appends = ['image_url'];
    public $hidden = ['image', 'created_at', 'updated_at'];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::deleted(function ($model) {
            // 删除所有关联文件
            $model->image() && $model->image()->delete();
            // $model->attrs()->detach();
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

    /**
     * 关联商品表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function goods()
    {
        return $this->belongsToMany('App\Models\Goods', 'goods', 'bar_code', 'bar_code');
    }


    /**
     * 保存图片
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
