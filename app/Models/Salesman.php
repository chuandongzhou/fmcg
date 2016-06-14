<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class Salesman extends Model
{
    use SoftDeletes;
    protected $table = 'salesman';

    protected $fillable = [
        'account',
        'password',
        'shop_id',
        'name',
        'avatar',
        'contact_information',
        'last_login_ip',
        'last_login_time',
        'status'
    ];

    protected $hidden = ['updated_at', 'created_at', 'last_login_ip', 'last_login_time'];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::deleted(function ($model) {
            $model->customers()->delete();
        });
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['avatar_url'];

    /**
     * 关联店铺
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }

    /**
     * 关联客户
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
        return $this->hasMany('App\Models\SalesmanCustomer');
    }

    /**
     * 设置头像
     *
     * @param mixed $file
     */
    public function setAvatarAttribute($file)
    {
        $file = config('path.upload_temp') . $file;
        try {
            $image = \Image::make($file);
        } catch (\Exception $e) {
            return;
        }
        $sizes = array_keys(cons('salesman.avatar'));
        $avatarPath = config('path.upload_salesman_avatar');

        rsort($sizes);
        if ($this->exists) {
            $uid = array_get($this->attributes, $this->primaryKey);
            $pathName = implode('/', divide_uid($uid, '_{size}.jpg'));

            // 创建目录
            $folder = $avatarPath . dirname($pathName);
            if (!is_dir($folder)) {
                mkdir($folder, 0770, true);
            }

            foreach ($sizes as $size) {
                $avatarFile = str_replace('{size}', $size, $pathName);
                $image->widen($size)->save($avatarPath . $avatarFile);
            }
        } else {
            static::created(function ($model) use ($file, $avatarPath, $sizes, $image) {
                $uid = array_get($model->attributes, $model->primaryKey);
                $pathName = implode('/', divide_uid($uid, '_{size}.jpg'));

                // 创建目录
                $folder = $avatarPath . dirname($pathName);
                if (!is_dir($folder)) {
                    mkdir($folder, 0770, true);
                }

                foreach ($sizes as $size) {
                    $avatarFile = str_replace('{size}', $size, $pathName);
                    $image->widen($size)->save($avatarPath . $avatarFile);
                }
            });
        }

    }

    /**
     * 密码自动转换
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    /**
     * Get user avatar url.
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        return salesman_avatar_url(array_get($this->attributes, $this->primaryKey), 128);
    }
}
