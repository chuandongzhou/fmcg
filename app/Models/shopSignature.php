<?php

namespace App\Models;



class ShopSignature extends Model
{
    protected $table = 'shop_signature';
    protected $timestamp = false;
    protected $fillable = [
        'text',
        'color',
        'shop_id',
        'signature'
    ];

    /**
     * 店铺
     */
    public function shop()
    {
        return $this->belongsTo('App\Model\shop');
    }

    /**
     * 图片
     * @return mixed
     */
    public function signature(){
        return $this->morphOne('App\Models\File', 'fileable');
    }

    /**
     * 设置店招图片
     * @param $signature
     * @return bool
     */
    public function setSignatureAttribute($signature){
        return $this->associateFile($this->convertToFile($signature), 'signature');
    }


    /**
     * 店招图片路径
     * @return string
     */
    public function getSignatureUrlAttribute(){
        $signature = $this->signature;

        return $signature ? $signature->url : '';
    }
}