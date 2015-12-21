<?php

namespace App\Models;


class Node extends Model
{
    protected $table = 'node';


    /**
     * 角色表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_node');
    }
}
