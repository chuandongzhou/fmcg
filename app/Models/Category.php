<?php

namespace App\Models;


class Category extends Model
{
    protected $table = 'category';
    public $timestamps = false;
    protected $fillable = ['pid', 'name', 'status', 'level'];
    public $hidden = ['status' , 'sort'];

    public static function boot(){
        parent::boot();
        static :: deleted(function ($model) {
            $model->attrs->delete();
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

}
