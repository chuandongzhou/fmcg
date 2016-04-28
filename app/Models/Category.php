<?php

namespace App\Models;


use Carbon\Carbon;

class Category extends Model
{
    public $timestamps = false;
    public $hidden = ['status', 'sort', 'icon_pic_url', 'icon'];
    protected $table = 'category';
    protected $fillable = ['pid', 'name', 'status', 'level', 'icon'];
    protected $appends = ['icon_url'];


    public static function boot()
    {
        parent::boot();
        static:: deleted(function ($model) {
            $model->attrs()->delete();
        });
    }

    /**
     * 标签表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attrs()
    {
        return $this->hasMany('App\Models\Attr');
    }

    /**
     *  图标
     */
    public function icon()
    {
        return $this->morphOne('App\Models\File', 'fileable');
    }

    /**
     * 广告
     *
     * @return mixed
     */
    public function adverts()
    {
        return $this->hasMany('App\Models\Advert')->where('type', cons('advert.type.category'))->ofTime(Carbon::now(),
            100);
    }

    /**
     * 设置图标
     *
     * @param $icon
     * @return bool
     */
    public function setIconAttribute($icon)
    {
        return $this->associateFile($this->convertToFile($icon), 'icon');
    }


    /**
     * 获取icon
     *
     * @return string
     */
    public function getIconUrlAttribute()
    {
        $icon = $this->icon;

        return $icon ? upload_file_url($icon->path) : '';
    }

}
