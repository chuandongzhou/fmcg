<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class WarehouseKeeper extends Model implements AuthenticatableContract
{
    use SoftDeletes;
    use Authenticatable;
    protected $table = 'warehouse_keeper';
    protected $fillable = [
        'account',
        'password',
    ];

}
