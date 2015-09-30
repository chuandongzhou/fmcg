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
     * @param string $myGoods
     * @return array
     */
    static function getGoodsBySearch($data, $myGoods = '=')
    {
        $goods = Goods::with('images')->select([
            'id',
            'name',
            'sales_volume',
            'price_retailer',
            'price_wholesaler',
            'is_new',
            'is_promotion'
        ])->where('user_type', $myGoods, auth()->user()->type);
        //排序
        if (isset($data['sort']) && in_array(strtolower($data['sort']), cons('goods.sort'))) {
            $goods = $goods->{'Order' . ucfirst($data['sort'])}();
        }
        // 省市县
        if (isset($data['province_id'])) {
            $goods = $goods->OfDeliveryArea($data);
        }
        // 名称
        if (isset($data['name'])) {
            $goods = $goods->where('name', 'like', '%' . $data['name'] . '%');
        }
        $attrs = [];
        $categories = Category::orderBy('level', 'asc')->select('name', 'level', 'id', 'pid')->get();
        if (isset($data['category_id'])) {
            //分类最高位为层级 后面为categoryId
            $level = substr($data['category_id'], 0, 1);
            $categoryId = substr($data['category_id'], 1);
            $categories = CategoryService::formatCategoryForSearch($categories->toArray(), $categoryId, $level);

            $attrs = (new AttrService([]))->getAttrByCategoryId($categoryId);
            $goods = $goods->OfCategory($categoryId, $level);
        }

        // 标签
        if (isset($data['attr'])) {
            $goods = $goods->OfAttr($data['attr']);
        }
        return ['goods' => $goods, 'attrs' => $attrs, 'categories' => $categories];
    }

}