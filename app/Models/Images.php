<?php

namespace App\Models;

use DB;

class Images extends Model
{
    protected $table = 'images';
    protected $fillable = [
        'cate_level_1',
        'cate_level_2',
        'cate_level_3',
        'image'
    ];
    public $appends = ['image_url'];
    public $hidden = ['image' , 'created_at', 'updated_at'];

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
        return $this->belongsToMany('App\Models\Goods');
    }

    /**
     * 关联标签表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attrs()
    {
        return $this->belongsToMany('App\Models\Attr', 'images_attr');
    }

    /**
     * @param $query
     * @param $attr
     */
    public function scopeOfAttr($query, $attr)
    {
        $goodsAttr = DB::table('images_attr')->select(DB::raw('images_id ,count(attr_id) as num'))->whereIn('attr_id',
            (array)$attr)->groupBy('images_id')->get();
        $goodsAttr = array_filter($goodsAttr, function ($item) use ($attr) {
            return $item->num == count($attr);
        });
        $goodsIds = array_pluck($goodsAttr, 'images_id');


        return $query->whereIn('id', $goodsIds);
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
     * 设置标签搜索
     *
     * @param $attr
     */
//    public function setAttrsAttribute($attr)
//    {
//        if (is_array($attr)) {
//            $this->attributes['attrs'] = json_encode($attr);
//        } else {
//            $this->attributes['attrs'] = '';
//        }
//    }

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
