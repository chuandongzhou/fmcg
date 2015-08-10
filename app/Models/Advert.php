<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/6
 * Time: 16:23
 */
namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    /**
     * @var string 广告表
     */
    protected $table = 'advert';
    protected $fillable = ['name', 'link_path', 'ad_type', 'type'];
}