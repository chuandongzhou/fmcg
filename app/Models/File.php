<?php

namespace App\Models;

use Config;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class File extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'file';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'fileable_id',
        'fileable_type',
        'name',
        'path',
        'size',
        'ext',
        'is_admin',
        'user_id',
        'ip',
        'uploaded_at',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['uploaded_at'];

    public $hidden = ['fileable_id', 'fileable_type'];

    /**
     * 复用模型关系
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function fileable()
    {
        return $this->morphTo();
    }

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册添加事件
        static::creating(function ($file) {
            $file->uploaded_at = $file->freshTimestamp();
        });

        // 注册删除事件
        static::deleting(function ($file) {
            // 删除文件
            $filePath = config('path.upload_file') . $file->path;
            @unlink($filePath);

            // 判断文件是否存在
            return !is_file($filePath);
        });
    }

    /**
     * 上传文件保存到数据库
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile
     * @return null|static
     */
    public static function createWithUploadedFile($uploadedFile)
    {
        if (!$uploadedFile instanceof UploadedFile) {
            return null;
        }

        if (!$uploadedFile->isValid()) {
            info($uploadedFile->getErrorMessage());

            return null;
        }

        return static::createWithFile($uploadedFile, $uploadedFile->getClientOriginalName());
    }

    /**
     * 保存文件到数据库
     *
     * @param string|\Symfony\Component\HttpFoundation\File\File $file
     * @param null|string $originalName
     * @param bool $saveToFileTable
     * @return array|null|static
     */
    public static function createWithFile($file, $originalName = null, $saveToFileTable = true)
    {
        if (is_string($file)) {
            $file = new SymfonyFile($file, false);
        }

        if (!$file->isFile()) {
            return null;
        }

        // 提取用户ID
        $userId = admin_auth()->id();
        if (!$isAdmin = boolval($userId)) {
            $userId = auth()->id();
        } else if (!boolval($userId)) {
            $userId = salesman_auth()->id();
        }
        $uploadFilePath = $saveToFileTable ? config('path.upload_file') : config('path.upload_temp');

        // 根据当前日期生成目录
        $date = date('Y/m/d/');

        $size = $file->getSize();
        if (!($ext = $file->guessExtension())) {
            $ext = $file->getExtension();
        }

        $name = uniqid() . '.' . $ext;

        try {
            // 移动到目录
            $file->move($uploadFilePath . $date, $name);
        } catch (FileException $e) {
            info($e);

            return null;
        }
        // 开始写入数据库
        $attributes = [
            'type' => 0,
            'fileable_id' => 0,
            'fileable_type' => '',
            'name' => $originalName ?: $file->getFilename(),
            'path' => $date . $name,
            'size' => $size,
            'ext' => $ext,
            'is_admin' => $isAdmin,
            'user_id' => intval($userId),
            'ip' => \Request::getClientIp(),
        ];
        return $saveToFileTable ? static::create($attributes) : $attributes;
    }

    /**
     * 获取url
     *
     * @return string
     */
    protected function getUrlAttribute()
    {
        if ($this->path) {
            $mtime = @filemtime(config('path.upload_file') . $this->path);
            return upload_file_url($this->path . ($mtime ? '?' . $mtime : ''));
        }

        return '';
    }
}
