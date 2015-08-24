<?php

namespace App\Models;


class OperationRecord extends Model
{
    protected $table = 'operation_record';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'reason',
        'content',
        'start_at',
        'end_at'
    ];
    protected $dates = [
        'start_at',
        'end_at'
    ];
}
