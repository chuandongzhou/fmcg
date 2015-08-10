<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attr extends Model
{
    protected $table = 'attr';
    public $timestamp = false;

    public function category()
    {
        return $this->belongsTo('app/Category');
    }
}
