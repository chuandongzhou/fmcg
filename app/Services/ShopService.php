<?php

namespace App\Services;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class ShopService
{

    protected $array = [];

    public function __construct($array)
    {
        $this->array = $array;
        return $this;
    }

    /**
     * 格式化标签
     *
     * @return array
     */
    public function formatImagePost()
    {
        $array = $this->array;
        if (!is_array($array)) {
            return [];
        }
        $imagesArr = [];
        foreach ($array['id'] as $key => $imageId) {
            $imagesArr[] = [
                'id' => $imageId,
                'path' => upload_file($array['path'][$key], 'temp'),
                'name' => $array['name'][$key]
            ];
        }
        return $imagesArr;
    }
}