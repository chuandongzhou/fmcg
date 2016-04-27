<?php

namespace App\Services;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class HomeColumnService
{
    /**
     * 验证id是否合法
     *
     * @param $idLists
     * @return bool
     */
    public function validateIdLists($idLists)
    {
        $idLists = explode('|', $idLists);
        $return = array_first($idLists, function ($key, $value) {
            return !is_numeric($value);
        });
        return count($idLists) <= 10 && is_null($return);
    }
}