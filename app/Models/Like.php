<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $table = 'like';
    public  $timestamp = false;

    public function user()
    {
        return $this->belongsTo('app/User');
    }
}
