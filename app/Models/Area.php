<?php

namespace App\Models;

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
