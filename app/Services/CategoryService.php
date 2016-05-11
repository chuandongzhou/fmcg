<?php

namespace App\Services;

use App\Models\Category;
use Cache;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class CategoryService
{

    /**
     * 组合一维数组
     *
     * @param $cate
     * @param string $html
     * @param int $pid
     * @param int $level
     * @return array
     */
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
     * 传递子分类的id返回所有的父级分类
     *
     * @param $cate
     * @param $id
     * @return array
     */
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

    /**
     * 传递父级id返回所有子级id
     *
     * @param $cate
     * @param $pid
     * @return array
     */
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

    /**
     * 传递父级id返回所有子级分类
     *
     * @param $cate
     * @param $pid
     * @return array
     */
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

    /**
     * 传递父级id返回所有子级分类
     *
     * @param $cate
     * @param $pid
     * @return array
     */
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
     * @param $id
     * @param $selected
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
     * @return array
     */
    static function formatCategoryForSearch($categories, $categoryId)
    {

        $parentNode = self::getParents($categories, $categoryId);
        if (empty($parentNode)) {
            return [];
        }
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

    /**
     * 获取categories
     *
     * @return array
     */
    static function getCategories()
    {
        $categories = [];
        $cacheConf = cons('category.cache');

        $cacheKey = $cacheConf['pre_name'] . 'category';
        if (Cache::has($cacheKey)) {
            $categories = Cache::get($cacheKey);
        } else {
            $categories = Category::active()->with('icon')->get()->each(function ($category) {
                $category->addHidden(['icon']);
            })->keyBy('id')->toArray();
            Cache::put($cacheKey, $categories, $cacheConf['expire']);
        }
        return $categories;
    }

    /**
     * 格式化分类
     *
     * @param $category
     * @return array
     */
    static function formatCategory($category)
    {
        return [
            'level' => substr($category, 0, 1),
            'category_id' => substr($category, 1)
        ];

    }

    /**
     * 格式化店铺商品的所有分类
     *
     * @param $shop
     * @param bool|false $cateId
     * @return array
     */
    static function formatShopGoodsCate($shop, $cateId = false)
    {
        $shopGoods = $shop->goods()->active()->get();
        $shopGoodsCates = $shopGoods->pluck('cate_level_1')->all();
        if ($cateId > 0 || !$cateId) {
            $cate2 = $shopGoods->pluck('cate_level_2')->all();
            $cate3 = $shopGoods->pluck('cate_level_3')->all();
            $cateId = self::formatCategory($cateId)['category_id'];
            $shopGoodsCates = array_unique(array_filter(array_merge($shopGoodsCates, $cate2, $cate3)));
        }
        $categories = array_where(self::getCategories(), function ($key, $value) use ($shopGoodsCates) {
            return in_array($key, $shopGoodsCates);
        });
        if ($cateId > 0) {
            return CategoryService::formatCategoryForSearch($categories, $cateId);
        } elseif (!$cateId) {
            return CategoryService::unlimitForLayer($categories);
        }
        return $categories;
    }

}