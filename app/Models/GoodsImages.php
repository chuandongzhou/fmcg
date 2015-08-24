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
        return $this->associateFile(upload_file($image, 'temp'), 'image');
    }

    /**
     * 设置标签搜索
     *
     * @param $attr
     */
    public function setAttrsAttribute($attr)
    {
        if (is_array($attr)) {
            $this->attributes['attrs'] = implode(',', $attr);
        } else {
            $this->attributes['attrs'] = '';
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
}
