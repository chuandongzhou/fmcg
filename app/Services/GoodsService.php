<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Goods;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class GoodsService
{

    /**
     * @param $data
     * @param string $goods
     * @return array
     */
    static function getGoodsBySearch($data, $goods)
    {
        //排序
        if (isset($data['sort']) && in_array(strtolower($data['sort']), cons('goods.sort'))) {
           $goods->{'Order' . ucfirst($data['sort'])}();
        }
        // 省市县
        if (isset($data['province_id'])) {
           $goods->OfDeliveryArea($data);
        }

        $attrs = [];
        $categories = Category::orderBy('level', 'asc')->select('name', 'level', 'id', 'pid')->get();
        if (isset($data['category_id'])) {
            //分类最高位为层级 后面为categoryId
            $level = substr($data['category_id'], 0, 1);
            $categoryId = substr($data['category_id'], 1);
            $categories = CategoryService::formatCategoryForSearch($categories->toArray(), $categoryId);

            $attrs = (new AttrService([]))->getAttrByCategoryId($categoryId);
            $goods->OfCategory($categoryId, $level);
        }

        // 标签
        if (isset($data['attr'])) {
           $goods->OfAttr($data['attr']);
        }

        // 名称
        if (isset($data['name'])) {
           $goods->where('name', 'like', '%' . $data['name'] . '%')->get();

             $goodsTemp = $goods->lists('cate_level_1','cate_level_2');

            $categoryIds = array_merge($goodsTemp->keys()->all() , $goodsTemp->all());
            $categories = $categories->filter(function ($val) use($categoryIds) {
               return in_array($val['id'] , $categoryIds);
            })->all();
        }


        $defaultAttrName = cons()->valueLang('attr.default');

        $searched = []; //保存已搜索的标签
        $moreAttr = []; //保存扩展的标签

        // 已搜索的标签
        foreach ($attrs as $key => $attr) {
            if (!empty($data['attr']) && in_array($attr['attr_id'], array_keys($data['attr']))) {
                $searched[$attr['attr_id']] = array_get($attr['child'], $data['attr'][$attr['attr_id']])['name'];
                unset($attrs[$key]);
            } elseif (!in_array($attr['name'], $defaultAttrName)) {
                $moreAttr[$key] = $attr;
                unset($attrs[$key]);
            }
        }
        return [
            'goods' => $goods,
            'attrs' => $attrs,
            'categories' => $categories,
            'searched' => $searched,
            'moreAttr' => $moreAttr
        ];
    }

}