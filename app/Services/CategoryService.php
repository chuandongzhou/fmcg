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

    //组合一维数组
    static function unlimitForLevel($cate, $html = '--', $pid = 0, $level = 0)
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v['level'] = $level + 1;
                $v['html'] = str_repeat($html, $level);
                $arr[] = $v;
                $arr = array_merge($arr, self::unlimitForLevel($cate, $html, $v['id'], $level + 1));
            }
        }

        return $arr;
    }

    //组合多维数组
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

    //传递子分类的id返回所有的父级分类
    static function getParents($cate, $id)
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['id'] == $id) {
                $arr[] = $v;
                $arr = array_merge(self::getParents($cate, $v['pid']), $arr);
            }
        }

        return $arr;
    }

    //传递父级id返回所有子级id
    static function getChildsId($cate, $pid)
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $arr[] = $v['id'];
                $arr = array_merge($arr, self::getChildsId($cate, $v['id']));
            }
        }
        return $arr;
    }

    //传递父级id返回所有子级分类
    static function getChilds($cate, $pid)
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $arr[] = $v;
                $arr = array_merge($arr, self::getChilds($cate, $v['id']));
            }
        }

        return $arr;
    }

    //传递父级id返回所有子级分类
    static function getChild($cate, $pid)
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $arr[] = $v;
                //$arr = array_merge($arr, self::getChilds($cate, $v['id']));
            }
        }

        return $arr;
    }

    /**
     * 获取所有同级分类
     *
     * @param $cate
     * @param $pid
     * @return array
     */
    static function getSiblings($cate, $id, $selected = 0)
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $id) {

                $v['id'] == $selected ? $arr['selected'] = $v : $arr[] = $v;
            }

        }

        return $arr;
    }

    /**
     * @param $categories
     * @param $categoryId
     * @param $level
     * @return array
     */
    static function formatCategoryForSearch($categories, $categoryId)
    {

        $parentNode = self::getParents($categories, $categoryId);

        $childNode = self::getChild($categories, $categoryId);
        $categoryArr[] = self::getSiblings($categories, $parentNode[0]['pid'], $parentNode[0]['id']);;
        if (count($parentNode) == 1) {
            $categoryArr[] = $childNode;
        } elseif (count($parentNode) == 2) {
            //获取所有同级
            $categoryArr[] = self::getSiblings($categories, $parentNode[0]['id'], $categoryId);
            $categoryArr[] = $childNode;
        } else {
            $categoryArr[] = self::getSiblings($categories, $parentNode[0]['id'], $parentNode[1]['id']);
            $categoryArr[] = self::getSiblings($categories, $parentNode[1]['id'], $categoryId);
        }
        return $categoryArr;
    }

}