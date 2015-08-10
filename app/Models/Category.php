<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';
    public $timestamps = false;

    public function attrs()
    {
        return $this->hasMany('app/Attr');
    }
}
