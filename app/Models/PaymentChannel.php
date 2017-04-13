<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentChannel extends Model
{
    use SoftDeletes;

    protected $table = 'payment_channel';

    protected $fillable = [
        'name',
        'identification_code',
        'type',
        'status',
        'icon'
    ];

    protected $appends = ['icon'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];


    /**
     * 获取所有app渠道
     *
     * @param $query
     * @return mixed
     */
    public function scopeApp($query)
    {
        return $query->active()->where('type', cons('payment_channel.type.app'));
    }

    /**
     * 设置icon
     *
     * @param mixed $file
     */
    public function setIconAttribute($file)
    {

        if (is_string($file)) {
            $file = config('path.upload_temp') . $file;
        } else {
            $result = $this->convertToFile($file, null, false);
            $file = $result ? $result['path'] : null;
            $file = config('path.upload_temp') . $file;
        }

        try {
            $image = \Image::make($file);
        } catch (\Exception $e) {
            return;
        }
        $sizes = cons('payment_channel.icon');
        $iconPath = config('path.payment_channel_icon');

        if ($this->exists) {
            $id = array_get($this->attributes, $this->primaryKey);
            $pathName = implode('/', divide_uid($id, '.jpg'));

            // 创建目录
            $folder = $iconPath . dirname($pathName);
            if (!is_dir($folder)) {
                mkdir($folder, 0770, true);
            }

            $image/*->resize($sizes['width'], $sizes['height'])*/
            ->save($iconPath . $pathName);

        } else {
            static::created(function ($model) use ($file, $iconPath, $sizes, $image) {
                $id = array_get($model->attributes, $model->primaryKey);
                $pathName = implode('/', divide_uid($id, '.jpg'));

                // 创建目录
                $folder = $iconPath . dirname($pathName);
                if (!is_dir($folder)) {
                    mkdir($folder, 0770, true);
                }

                $image->resize($sizes['width'], $sizes['height'])->save($iconPath . $pathName);
            });
        }
        @unlink($file);
    }

    /**
     * 获取图片
     *
     * @return string
     */
    public function getIconAttribute()
    {
        return payment_channel_icon_url(array_get($this->attributes, $this->primaryKey));
    }
}
