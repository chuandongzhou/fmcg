<?php

namespace App\Models;


class Feedback extends Model
{
    protected $table = 'feedback';
    protected $fillable = [
        'account',
        'name',
        'content',
        'status',
    ];
}
