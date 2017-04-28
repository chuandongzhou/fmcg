<?php

namespace App\Models;


class RenewRecord extends Model
{
    protected $table = 'renew_record';

    protected $fillable = [
        'renew_type',
        'cost',
        'detail',
        'old_expire_at'
    ];

    protected $dates = ['old_expire_at'];

}
