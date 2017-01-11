<?php

namespace App\Models;


use App\Http\Controllers\Api\V1\CartController;
use Carbon\Carbon;

class VersionRecord extends Model
{
    //基本配置
    protected $table = 'version_record';
    protected $fillable = [
        'version_no',
        'version_name',
        'type',
        'user_name',
        'content'
    ];

    /**
     * 设置type
     *
     * @param $type
     */
    public function setTypeAttribute($type)
    {
        $this->attributes['type'] = cons('push_device.' . $type);
        $this->attributes['user_name'] = admin_auth()->user()->name;
    }

    /**
     * 按时间搜索
     *
     * @param $query
     * @param $dates
     * @return mixed
     */
    public function scopeOfDates($query, $dates)
    {
        return $query->where(function ($query) use ($dates) {
            if ($beginDay = array_get($dates, 'begin_day')) {
                $query->where('created_at', '>', $beginDay);
            }
            if ($endDay = array_get($dates, 'end_day')) {
                $endDay = (new Carbon($endDay))->endOfDay();
                $query->where('created_at', '<', $endDay);
            }
        });
    }

}
