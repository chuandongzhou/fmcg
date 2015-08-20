<?php

namespace App\Models;


class Advert extends Model
{
    //对应表
    protected $table = 'advert';
    //不批量更新字段
//    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'link_path',
        'ad_type',
        'time_type',
        'app_type',
        'started_at',
        'end_at',
        'image',
    ];
    //关闭自动维护时间戳
    public $timestamps = false;

    /**
     * 关联广告图片文件
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function image()
    {
        return $this->morphOne('App\Models\File', 'fileable');
    }


    /**
     * 模型关联一个文件
     *
     * @param $image
     * @return bool
     */
    public function setImageAttribute($image)
    {

        return $this->associateFile(upload_file($image, 'temp'), 'image');
    }

    /**
     * 获取格式化后的广告信息
     *
     * @param $type
     * @return mixed
     */
    public function formatDuration($type)
    {
        $records = $this->whereRaw($this->cate($type))->paginate(5);
        foreach ($records as $value) {
            $value->image()->max('id');
        }

        return $records;
    }

    /**
     * 添加查询条件
     *
     * @param $type
     * @return string
     */
    public function cate($type)
    {
        switch ($type) {
            case 'home':
                $option = 'ad_type = 0 and app_type = 0';
                break;
            case 'retailer':
                $option = 'ad_type != 0 and app_type = 0';
                break;
            default:
                $option = 'ad_type != 0 and app_type != 0';
        }

        return $option;
    }


}
