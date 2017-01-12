<?php

namespace App\Services;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class GoodsImageService extends RedisService
{

    protected $key = 'goods:image:';


    /**
     * 获取
     *
     * @param $goodsId
     * @return int|string
     */
    public function getImage($goodsId)
    {
        $key = $this->key . $goodsId;
        return $this->get($key);
    }

    public function hasImage($goodsId)
    {
        $key = $this->key . $goodsId;
        return $this->has($key);
    }

    /**
     * 设置商品图片
     *
     * @param $goodsId
     * @param $url
     * @return int
     */
    public function setImage($goodsId, $url)
    {
        $key = $this->key . $goodsId;
        return $this->setRedis($key, $url, 86400);
    }

}