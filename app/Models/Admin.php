<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';
    protected $fillable = [
        'user_name',
        'real_name',
        'password',
        'role_id',
        'last_login_ip',
        'last_login_time',
        'email',
        'remark',
        'status',
        'info'
    ];
    protected $hidden = ['password'];

    /**
     * role表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    /**
     * 设置密码时候进行哈希处理
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }
}
