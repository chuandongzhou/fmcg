<?php

namespace App\Models;


class Role extends Model
{
    protected $table = 'role';
    protected $fillable = ['name', 'status', 'remark'];
    protected $hidden = ['created_at'];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::deleted(function ($model) {
            // 删除所有关联文件
            $model->nodes()->detach();
        });
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
