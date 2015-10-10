<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushDevice extends Model
{
    //
    protected $table = 'push_device';
    protected $fillable = [
        'user_id',
        'type',
        'version',
        'token',
        'send_count'
    ];
    protected $date = ['created_at', 'updated_at'];

    /**
     * 该设备所属角色
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
