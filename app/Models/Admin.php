<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';
    protected $fillable = [
        'role_id',
        'user_name',
        'real_name',
        'password',
        'last_login_ip',
        'last_login_time',
        'email',
        'remark',
        'status',
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
}
