<?php

namespace App\Models;

class WeixinArticle extends Model
{
    protected $table = 'weixin_article';
    protected $fillable = [
        'title',
        'link_url',
        'image'
    ];
    public $appends = ['image_url'];
    public $hidden = ['image', 'created_at', 'updated_at'];

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
        return $image ? $image->url : '';
    }
}
