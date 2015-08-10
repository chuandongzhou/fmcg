<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';
    protected $fillable = ['name', 'status', 'remark'];
    protected $hidden = ['created_at'];

    /**
     * User表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('app\Models\User');
    }

    /**
     * 节点点
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function nodes()
    {
        return $this->belongsToMany('App\Models\Node', 'role_node');
    }

    /**
     * Admin表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function admins()
    {
        return $this->hasMany('App\Models\Admin');
    }
}
