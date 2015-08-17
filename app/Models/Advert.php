<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    //对应表
    protected $table = 'advert';
    //不批量更新字段
    protected $guarded = ['id'];
    //关闭自动维护时间戳
    public $timestamps = false;

    /**
     * 关联广告图片文件
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function file()
    {
        return $this->hasOne('App\Models\File', 'fileable_id', 'id');
    }

    public function formatDuration()
    {
        $now = date('Y-m-d H:i:s');
        $records = $this->get();
        foreach ($records as &$value) {
            if ($value->time_type == cons('advert.time_type.forever')) {
                $value->time_type = '永久';
                $value->started_at = '';
                $value->end_at = '';
            } else {
                if ($value->started_at > $now) {
                    $value->time_type = '未开始';
                } elseif ($value->end_at < $now) {
                    $value->time_type = '已结束';
                } else {
                    $value->time_type = '展示中';
                }
            }
        }

//        dd($records);
        return $records;
    }
}
