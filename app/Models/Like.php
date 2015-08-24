<?php

namespace App\Models;

class Like extends Model
{
    protected $table = 'like';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type',
        'created_at'
    ];

    /**
     * 用户表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
