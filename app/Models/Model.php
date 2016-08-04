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
     * 将文件ID、路径、临时文件转成FileModel
     *
     * @param int|string|\SplFileInfo $file
     * @param string $originalName
     * @param bool $saveToFileTable
     * @return null|\App\Models\File
     */
    public function convertToFile($file, $originalName = null, $saveToFileTable = true)
    {
        if (empty($file)) {
            return null;
        }

        // 根据ID查询FileModel
        if (is_numeric($file)) {
            $file = File::where('id', $file)->where('fileable_id', 0)->first(['id']);
        } else if (is_string($file)) {
            if (!is_file($file)) {
                $file = upload_file($file, 'temp');
            }
        }

        return File::createWithFile($file, $originalName, $saveToFileTable);
    }

    /**
     * 模型关联一个文件
     *
     * @param \App\Models\File $file
     * @param string $relate
     * @param int $fileType
     * @return bool
     */
    public function associateFile($file, $relate, $fileType = 0)
    {
        if ($file && !$file instanceof File) {
            throw new \LogicException('File must null or instance of FileModel.');
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
     * 模型关联多个文件
     *
     * @param array|\SplFileInfo $files $files [1] , [$path] , [['id'=>0 , 'path'=>'and.png']]
     * @param string $relate
     * @param int $fileType
     * @param bool $isOnly
     * @return bool
     */
    public function associateFiles($files, $relate, $fileType = 0, $isOnly = true)
    {
        foreach ($files as $fileItem) {
            if (is_numeric($fileItem)) {
                $file = File::where(['id' => $fileItem, 'fileable_id' => 0])->first(['id']);
            } elseif (is_array($fileItem)) {
                if ($id = $fileItem['id']) {
                    $file = File::where(['id' => $id])->first(['id']);
                    $file && ($file->name = $fileItem['name']);
                } else {
                    $file = File::createWithFile($fileItem['path'], $fileItem['name'] ?: '');
                }
            } else {
                //文件地址或文件
                $file = File::createWithFile($fileItem);
            }
            // 查出当前正在使用的附件
            if ($isOnly && $oldFile = $this->$relate()->where('type', $fileType)->first()) {
                if ($file && $oldFile->id == $file->id) {
                    return true;
                }

                $oldFile->delete();
            }
            if ($file) {
                $file->type = $fileType;
                if ($this->exists) {
                    $this->$relate()->where('id', $file->id)->save($file);
                } else {
                    static::created(function ($model) use ($relate, $file) {
                        $model->$relate()->where('id', $file->id)->save($file);
                    });
                }
            }
        }
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