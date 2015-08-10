<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';
    public $timestamp = false;

    public function user()
    {
        return $this->belongsTo('app/User');
    }
}
