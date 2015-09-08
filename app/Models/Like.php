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
    public static function boot(){
        parent ::boot();
        static::creating(function($model){
            $model->user_id = auth()->user()->id;
        });
    }

    /**
     * 用户表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function likeable(){
       return $this->morphTo();
    }
}
