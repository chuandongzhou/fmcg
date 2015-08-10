<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivingAddress extends Model
{
    //
    protected $table = 'receivingAddress';

    public function user()
    {
        return $this->belongsTo('app/User');
    }
}
