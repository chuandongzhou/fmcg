<?php

namespace App\Models;


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
    protected $fillable = ['name', 'type', 'url', 'start_at', 'end_at', 'image'];

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

    public function scopeOfTime($query, $nowTime)
    {
        return $query->where('start_at', '<', $nowTime)->Where(function ($query) use ($nowTime) {
            $query->where('end_at', '>', $nowTime)->orWhere('end_at', null);
        })->orderBy('id' , 'desc')->take(4);
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
     * 设置结束时间
     *
     * @param $endAt
     */
    public function setEndAtAttribute($endAt)
    {
        if (empty($endAt)) {
            $this->attributes['end_at'] = null;
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
