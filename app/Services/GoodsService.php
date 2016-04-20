<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Goods;
use App\Models\HomeColumn;
use Cache;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class GoodsService
{
    /**
     * 根据搜索获取商品
     *
     * @param $data
     * @param $goods
     * @param bool|true $isWeb
     * @return array
     */
    static function getGoodsBySearch($data, $goods, $isWeb = true)
    {
        //排序
        if (isset($data['sort']) && in_array(strtolower($data['sort']), cons('goods.sort'))) {
            $goods->{'Of' . ucfirst(camel_case($data['sort']))}();
        }
        // 省市县

        if (isset($data['province_id'])) {
            $goods->OfDeliveryArea(array_filter($data));
        }

        $attrs = [];
        $resultCategories = [];
        $categories = collect(CategoryService::getCategories())->sortBy('level')->toArray();
        if (isset($data['category_id'])) {
            //分类最高位为层级 后面为categoryId
            $level = substr($data['category_id'], 0, 1);
            $categoryId = substr($data['category_id'], 1);
            $resultCategories = CategoryService::formatCategoryForSearch($categories, $categoryId);
            $attrs = (new AttrService([]))->getAttrsByCategoryId($categoryId);
            $goods->OfCategory($categoryId, $level);
        }

        // 标签
        if (isset($data['attr']) && !empty($data['attr'])) {
            $goods->OfAttr($data['attr']);
        }

        // 名称
        if (isset($data['name']) && $data['name']) {
            $cachePre = cons('goods.cache.keywords_pre');

            $cacheKey = $cachePre . 'sort';
            $keywords = Cache::get($cacheKey);
            $keywords[(string)$data['name']] = isset($keywords[$data['name']]) ? $keywords[$data['name']] + 1 : 1;
            Cache::forever($cacheKey, $keywords);

            $goods->where('name', 'like', '%' . $data['name'] . '%')->get();

            $categoryIds = array_unique($goods->lists('cate_level_1')->toArray());

            $categories = array_filter($categories, function ($val) use ($categoryIds) {
                return in_array($val['id'], $categoryIds);
            });
        }


        $defaultAttrName = cons()->valueLang('attr.default');

        $searched = []; //保存已搜索的标签
        $moreAttr = []; //保存扩展的标签

        if ($isWeb) {
            $attrs = (new AttrService($attrs))->format();
            // 已搜索的标签
            foreach ($attrs as $key => $attr) {
                if (!empty($data['attr']) && in_array($attr['attr_id'], array_keys((array)$data['attr']))) {
                    $searched[$attr['attr_id']] = array_get($attr['child'], $data['attr'][$attr['attr_id']])['name'];
                    unset($attrs[$key]);
                } elseif (!in_array($attr['name'], $defaultAttrName)) {
                    $moreAttr[$key] = $attr;
                    unset($attrs[$key]);
                }
            }
        } else {
            //手机端有搜索名字时才返回
            $categories = isset($data['name']) ? $categories : new \stdClass();
        }

        return [
            'goods' => $goods,
            'attrs' => $attrs,
            'categories' => isset($data['category_id']) ? $resultCategories : array_where($categories,
                function ($key, $value) {
                    return $value['pid'] === 0;
                }),
            'searched' => $searched,
            'moreAttr' => $moreAttr
        ];
    }

    /**
     * 新版商品栏目
     *
     * @return array
     */
    static function getNewGoodsColumn()
    {
        $user = auth()->user();
        $userTypes = cons('user.type');
        $type = is_null($user) ? $userTypes['retailer'] : $user->type;

        //供应商暂时与批发商一致
        $type = $type <= $userTypes['wholesaler'] ? $type : $userTypes['wholesaler'];

        $addressData = (new AddressService)->getAddressData();
        $data = array_except($addressData, 'address_name');

        $homeColumnGoodsConf = cons('home_column.goods');
        $cacheKey = $homeColumnGoodsConf['cache']['pre_name'] . $type . ':' . $data['province_id'] . ':' . $data['city_id'];
        $goodsColumns = [];
        if (Cache::has($cacheKey)) {
            $goodsColumns = Cache::get($cacheKey);
        } else {
            //商品
            $goodsColumns = Category::active()->where('pid', 0)->with('adverts.image')->get([
                'id',
                'level',
                'pid',
                'name'
            ])->each(function ($category) {
                $category->setAppends([]);
            });
            $displayCount = $homeColumnGoodsConf['count']; //显示条数

            $goodsFields = [
                'id',
                'name',
                'bar_code',
                'price_retailer',
                'price_wholesaler',
                'min_num_retailer',
                'min_num_wholesaler',
                'is_new',
                'is_out',
                'is_promotion',
                'sales_volume',
                'cate_level_1'
            ];
            $goods = Goods::active()->whereIn('cate_level_1', $goodsColumns->pluck('id')->all())
                ->where('user_type', '>', $type)
                ->OfDeliveryArea(array_filter($data))
                ->with('images.image')
                ->select($goodsFields)
                ->get()->each(function ($goods) {
                    $goods->setAppends(['image_url']);
                });
            foreach ($goodsColumns as $category) {
                $category->goods = $goods->where('cate_level_1', $category['id'])->take($displayCount);
            }
            Cache::put($cacheKey, $goodsColumns, $homeColumnGoodsConf['cache']['expire']);
        }
        return $goodsColumns;
    }
    /**
     * 增加商品销量
     *
     * @param $orderGoodsNum
     * @return bool
     */
    static function addGoodsSalesVolume($orderGoodsNum)
    {
        if (empty($orderGoodsNum)) {
            return false;
        }
        foreach ($orderGoodsNum as $goodsId => $goodsNum) {
            Goods::where('id', $goodsId)->increment('sales_volume', $goodsNum);
        }
        return true;
    }


    /**
     * 根据goods获取所有分类
     *
     * @param \App\Models\Goods $goods
     * @return array
     */
    static function getGoodsCate(Goods $goods)
    {
        $cateIds = [
            $goods->cate_level_1,
            $goods->cate_level_2,
            $goods->cate_level_3,
        ];

        $categories = CategoryService::getCategories();

        $goodsCates = collect($categories)->filter(function ($category) use ($cateIds) {
            return in_array($category['id'], array_filter($cateIds));
        })->sortBy('level')->keyBy('id');

        return $goodsCates;

    }

}