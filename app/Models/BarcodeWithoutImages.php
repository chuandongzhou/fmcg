<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/12/8
 * Time: 15:30
 */
namespace App\Models;

class BarcodeWithoutImages extends Model
{
    protected $table = 'barcode_without_images';
    protected $fillable = ['barcode'];
}