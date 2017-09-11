<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'business_areas';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'name',
        'remark'
    ];
}
