<?php

namespace App\Models;

class UserToken extends Model
{
    protected $table = 'user_token';
    protected $fillable = [
        'login_count',
        'token',
        'type'
    ];

    /**
     * 关联user 表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 按类型查找
     *
     * @param $query
     * @param int $type
     * @return mixed
     */
    public function scopeOfType($query, $type = 0)
    {
        return $query->whereType((int)$type);
    }
}
