<?php

namespace App\Models;


class VersionRecord extends Model
{
    //基本配置
    protected $table = 'version_record';
    protected $fillable = [
        'version_no',
        'version_name',
        'type',
        'user_name',
        'content'
    ];
}
