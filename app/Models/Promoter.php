<?php

namespace App\Models;


class Promoter extends Model
{
    protected $table = 'promoter';
    protected $fillable = [
        'name',
        'contact',
        'spreading_code'
    ];
}
