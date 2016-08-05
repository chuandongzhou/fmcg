<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Goods;
use App\Models\GoodsColumn;
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
        /**
         * 状态
         */
        if (isset($data['status'])) {
            $goods->where('status', $data['status']);
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
        // 省市
        if (isset($data['province_id'])) {
            $goods->OfDeliveryArea(array_filter($data));
        }
        //排序
        if (isset($data['sort']) && in_array(strtolower($data['sort']), cons('goods.sort'))) {
            $goods->{'Of' . ucfirst(camel_case($data['sort']))}();
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
        $goods->OfCommonSort();
        return [
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
     * 获取店铺商品
     *
     * @param $shop
     * @param $data
     * @param $with
     * @return array
     */
    static function getShopGoods($shop, $data = [], $with = [])
    {
        $goods = $shop->goods()->with($with);
        /**
         * 状态
         */
        if (isset($data['status'])) {
            $goods->OfStatus($data['status']);
        }
        $attrs = [];
        if (isset($data['category_id'])) {
            //分类最高位为层级 后面为categoryId
            $cateArr = CategoryService::formatCategory($data['category_id']);
            $attrs = (new AttrService([]))->getAttrsByCategoryId($cateArr['category_id']);
            $goods->OfCategory($cateArr['category_id'], $cateArr['level']);
        }
        // 标签
        if (isset($data['attr']) && !empty($data['attr'])) {
            $goods->OfAttr($data['attr']);
        }

        // 名称
        if (isset($data['name']) && $data['name']) {
            $goods->where('name', 'like', '%' . $data['name'] . '%')->get();
        }
        // 省市
        if (isset($data['province_id'])) {
            $goods->OfDeliveryArea(array_filter($data));
        }
        //排序
        if (isset($data['sort']) && in_array(strtolower($data['sort']), cons('goods.sort'))) {
            $goods->{'Of' . ucfirst(camel_case($data['sort']))}();
        }
        $attrs = (new AttrService($attrs))->format();

        $defaultAttrName = cons()->valueLang('attr.default');
        $searched = []; //保存已搜索的标签
        $moreAttr = []; //保存扩展的标签
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
        return [
            'goods' => $goods->OfCommonSort(),
            'attrs' => $attrs,
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
        $cacheKey = $homeColumnGoodsConf['cache']['name_cate'] . $type . ':' . $data['province_id'] . ':' . $data['city_id'];
        $goodsColumns = [];
        if (Cache::has($cacheKey)) {
            $goodsColumns = Cache::get($cacheKey);
        } else {
            //商品
            $goodsColumns = Category::active()
                ->where('level', 1)
                ->with('adverts.image')
                ->get(['id', 'level', 'pid', 'name'])
                ->each(function ($category) use ($data) {
                    $category->setAppends([]);
                    if ($category->level == 1) {

                        $category->adverts = $category->adverts->filter(function ($advert) use ($data) {
                            if (is_null($advert['province_id'])) {
                                return true;
                            }
                            if ($data['province_id'] && $data['city_id']) {
                                return $advert['province_id'] == $data['province_id'] && $advert['city_id'] == $data['city_id'];
                            }
                            return $advert['province_id'] == $data['province_id'];
                        })->take(5);
                    }
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
                'pieces_retailer',
                'pieces_wholesaler',
                'is_new',
                'is_out',
                'is_promotion',
                'sales_volume',
                'cate_level_1'
            ];
            $columnIds = GoodsColumn::OfAddress(array_filter($data))->lists('id_list', 'cate_level_1');

            foreach ($goodsColumns as $category) {
                $columnId = isset($columnIds[$category['id']]) ? $columnIds[$category['id']] : null;
                $columnGoods = collect([]);
                $categoryGoods = collect([]);
                if (!is_null($columnId)) {
                    $columnGoods = Goods::active()->whereIn('id', $columnId)
                        ->where('user_type', '>', $type)
                        ->where('is_out', 0)
                        ->with('images.image')
                        ->orderBy('is_promotion', 'desc')
                        ->orderBy('is_new', 'desc')
                        ->latest()
                        ->select($goodsFields)
                        ->take($displayCount)
                        ->get()
                        ->each(function ($goods) {
                            $goods->setAppends(['image_url']);
                        });
                }
                $columnGoodsCount = $columnGoods->count();
                if ($columnGoodsCount < $displayCount) {
                    $categoryGoods = Goods::active()
                        ->where('cate_level_1', $category['id'])
                        ->where('user_type', '>', $type)
                        ->where('is_out', 0)
                        ->whereNotIn('id', (array)$columnId)
                        ->OfDeliveryArea(array_filter($data))
                        ->with('images.image')
                        ->orderBy('is_promotion', 'desc')
                        ->orderBy('is_new', 'desc')
                        ->latest()
                        ->select($goodsFields)
                        ->take($displayCount - $columnGoodsCount)
                        ->get()
                        ->each(function ($goods) {
                            $goods->setAppends(['image_url']);
                        });
                }

                $category->goods = $columnGoods->merge($categoryGoods);
            }
            Cache::put($cacheKey, $goodsColumns, $homeColumnGoodsConf['cache']['expire']);
        }
        return $goodsColumns;
    }

    /**
     * 获取首页商品栏目
     *
     * @return mixed
     */
    static function getGoodsColumn()
    {
        $user = auth()->user();
        $userTypes = cons('user.type');
        $type = is_null($user) ? $userTypes['retailer'] : $user->type;

        //供应商暂时与批发商一致
        $type = $type <= $userTypes['wholesaler'] ? $type : $userTypes['wholesaler'];

        $columnTypes = cons('home_column.type');

        $homeColumnGoodsConf = cons('home_column.goods');

        $goodsColumns = [];
        $addressData = (new AddressService)->getAddressData();
        $data = array_except($addressData, 'address_name');

        $cacheKey = $homeColumnGoodsConf['cache']['name_admin'] . $type . ':' . $data['province_id'] . ':' . $data['city_id'];

        if (Cache::has($cacheKey)) {
            $goodsColumns = Cache::get($cacheKey);
        } else {
            //商品
            $goodsColumns = HomeColumn::where('type', $columnTypes['goods'])->get();

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
            $displayCount = $homeColumnGoodsConf['count']; //显示条数
            foreach ($goodsColumns as $goodsColumn) {
                $goods = Goods::active()->whereIn('id', $goodsColumn->id_list)
                    ->where('user_type', '>', $type)
                    ->OfDeliveryArea($data)
                    ->with('images.image')
                    ->select($goodsFields)
                    ->get()->each(function ($goods) {
                        $goods->setAppends(['image_url']);
                    });
                $columnGoodsCount = $goods->count();

                if ($columnGoodsCount < $displayCount) {
                    $columnGoodsIds = $goods->pluck('id')->toArray();
                    $goodsBySort = Goods::active()->whereNotIn('id', $columnGoodsIds)
                        ->where('user_type', '>', $type)
                        ->{'Of' . ucfirst(camel_case($goodsColumn->sort))}()
                        ->with('images.image')
                        ->OfDeliveryArea($data)
                        ->select($goodsFields)
                        ->take($displayCount - $columnGoodsCount)
                        ->get()->each(function ($goods) {
                            $goods->setAppends(['image_url']);
                        });
                    $goods = $goods->merge($goodsBySort);
                }
                $goodsColumn->goods = $goods;
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