<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Cache;

class Model extends Eloquent
{

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::deleted(function ($model) {
            // 删除所有关联文件
            if (!$model instanceof File) {
                $model->morphMany('App\Models\File', 'fileable')->delete();
            }
        });
    }

    /**
     * 模型关联一个文件
     *
     * @param int|string|\App\Models\File $file
     * @param string $relate
     * @param int $fileType
     * @return bool
     */
    public function associateFile($file, $relate, $fileType = 0)
    {
        if ($file) {
            if (is_numeric($file)) {
                $file = File::where('id', $file)->where('fileable_id', 0)->first(['id']);
            } else {
                if (is_file($file)) {
                    $file = File::createWithFile($file);
                }
            }

            if (!$file instanceof File) {
                $file = null;
            }
        }

        // 查出当前正在使用的附件
        if ($oldFile = $this->$relate) {
            if ($file && $oldFile->id == $file->id) {
                return true;
            }

            $oldFile->delete();
        }
        // 更新附件
        if ($file) {
            $file->type = $fileType;
            if ($this->exists) {
                $this->$relate()->save($file);
            } else {
                static::created(function ($model) use ($relate, $file) {
                    $model->$relate()->save($file);
                });
            }
        }

        return true;
    }

    /**
     * 模型关联一个文件
     *
     * @param int $filesId
     * @param string $relate
     * @param int $fileType
     * @return bool
     */
    public function associateFiles($filesId, $relate, $fileType = 0)
    {

    }

    /**
     * 条件 活跃的
     *
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * 判断是否激活
     *
     * @return bool
     */
    public function isActive()
    {
        return 1 === intval($this->getAttribute('status'));
    }

}