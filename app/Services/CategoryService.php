<?php

namespace App\Services;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class CategoryService
{

    /**
     * 组合多维数组
     *
     * @param $cate
     * @param int $pid
     * @param string $name
     * @return array
     */
    static function unlimitForLayer($cate, $pid = 0, $name = 'child')
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v[$name] = self::unlimitForLayer($cate, $v['id'], $name);
                $arr[] = $v;
            }
        }
        return $arr;
    }

    /**
     * 传递父级id返回所有子级id
     *
     * @param $cate
     * @param $pid
     * @return array
     */
    static function getChildsId($cate, $pid)
    {
        $arr = array($pid);
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $arr[] = $v['id'];
                $arr = array_merge($arr, self::getChildsId($cate, $v['id']));
            }
        }

        return $arr;
    }
}