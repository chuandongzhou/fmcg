<?php

namespace App\Models;


class Address extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'address';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'pid', 'pin'];

    public $hidden = ['pin'];

    public $timestamps = false;
}
