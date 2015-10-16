<?php

namespace App\Models;


class DeliveryArea extends Model
{
    public $coor;
    protected $table = 'delivery_area';
    protected $fillable = [
        'type',
        'addressable_id',
        'addressable_type',
        'province_id',
        'city_id',
        'district_id',
        'street_id',
        'area_name',
        'address',
        'coordinate'
    ];
    protected $hidden = [
        'type',
        'addressable_type',
        'addressable_id',
        'created_at',
        'updated_at'
    ];
//
//public function __construct($array = [])
//{
//    if(isset($array['coordinate'])){
//        $this->coor = $array['coordinate'];
//        unset($array['coordinate']);
//    }
//    parent::__construct($array);
//}
    public function setCoordinateAttribute($coordinate)
    {
        dd($coordinate);
        if (empty($coordinate)) {
            return;
        }
//        static::saved(function ($model) use ($coordinate) {
//            $model->coordinate()->save(new Coordinate($coordinate));
//        });
        $this->coor = $coordinate;

    }



    public static function boot()
    {
        parent::boot();
        // 注册删除事件
        static::deleting(function ($model) {
        $model->coordinate()->delete();

    });
        static::saved(function ($model) {
            dd($model->coor);
//            $model->coordinate()->save(new Coordinate($coordi));
        });
    }

    /**
     * 复用模型关系
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function addressable()
    {
        return $this->morphTo();
    }

    /**
     * 配送区域的经纬度,一个区域对应一条记录
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function coordinate()
    {
        return $this->hasOne('App\Models\Coordinate', 'delivery_area_id', 'id');
    }
}
