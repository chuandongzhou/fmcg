<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Goods;
use App\Models\GoodsColumn;
use App\Models\Order;
use Cache;
use Mockery\CountValidator\Exception;

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
            $goods->ofDeliveryArea(array_filter($data));
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
        $goods->hasPrice()->OfCommonSort();
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
        $goods = $shop->goods()->ofGift(array_get($data, 'is_gift'))->with($with);
        /**
         * 状态
         */
        if (isset($data['status'])) {
            $goods->ofStatus($data['status']);
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
        //名字或者二维码
        if (isset($data['nameOrCode']) && !empty($data['nameOrCode'])) {
            $goods->ofNameOrCode($data['nameOrCode']);
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
            'goods' => $goods,
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
        //$type = $type <= $userTypes['wholesaler'] ? $type : $userTypes['wholesaler'];

        $addressData = (new AddressService)->getAddressData();
        $data = array_except($addressData, 'address_name');

        $homeColumnGoodsConf = cons('home_column.goods');
        /*$cacheKey = $homeColumnGoodsConf['cache']['name_cate'] . $type . ':' . $data['province_id'] . ':' . $data['city_id'];

       $goodsColumns = [];
       / (Cache::has($cacheKey)) {
           $goodsColumns = Cache::get($cacheKey);
       } else {*/
        //商品
        $goodsColumns = Category::active()
            ->where('level', 1)
            ->with(['adverts.image', 'leftAdverts.image'])
            ->get(['id', 'level', 'pid', 'name'])
            ->each(function ($category) use ($data) {
                $category->setAppends([]);
                if ($category->level == 1) {
                    $category->adverts = $category->adverts->filter(function ($advert) use ($data) {
                        if (empty($advert['province_id'])) {
                            return true;
                        }
                        if ($data['province_id'] && $data['city_id']) {
                            return $advert['province_id'] == $data['province_id'] && $advert['city_id'] == $data['city_id'];
                        }
                        return $advert['province_id'] == $data['province_id'];
                    })->take(5);
                }

                $category->leftAdverts = $category->leftAdverts->filter(function ($leftAdvert) use ($data) {
                    if (empty($leftAdvert['province_id'])) {
                        return true;
                    }
                    if ($data['province_id'] && $data['city_id']) {
                        return $leftAdvert['province_id'] == $data['province_id'] && $leftAdvert['city_id'] == $data['city_id'];
                    }
                    return $leftAdvert['province_id'] == $data['province_id'];
                })->first();
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
                    ->ofSearchType($type)
                    ->where('is_out', 0)
                    ->ofCommonSort()
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
                    ->ofSearchType($type)
                    ->where('is_out', 0)
                    ->whereNotIn('id', (array)$columnId)
                    ->OfDeliveryArea(array_filter($data))
                    ->ofCommonSort()
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
        /* Cache::put($cacheKey, $goodsColumns, $homeColumnGoodsConf['cache']['expire']);
     }*/
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
        //$type = $type <= $userTypes['wholesaler'] ? $type : $userTypes['wholesaler'];

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
                    ->ofSearchType($type)
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
                        ->ofSearchType($type)
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
     * @param \App\Models\Order $order
     * @return bool
     */
    static function addGoodsSalesVolume(Order $order)
    {
        if (is_null($order)) {
            return false;
        }
        foreach ($order->goods as $goods) {
            $goods->increment('sales_volume', $goods->pivot->num);
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

    /**
     *
     * 获得商品单位等级进制
     *
     * @param $goods
     * @param $piecesValue
     * @return int
     */
    static public function getPiecesSystem($goods, $piecesValue = '')
    {
        $pieces = $goods->goodsPieces->toArray();
        $level = array_search($piecesValue, $pieces);
        switch (substr($level, -1)) {
            case 2 :
                $system = $pieces['system_2'] == null ? 1 : $pieces['system_2'];
                break;
            case 1 :
                $system = (($pieces['system_1'] == null ? 1 : $pieces['system_1']) * ($pieces['system_2'] == null ? 1 : $pieces['system_2']));
                break;
            default :
                $system = 1;
                break;
        }
        return $system;

    }

    /**
     *
     * 获得商品最小单位单位代码
     *
     * @param $goodsId
     * @param $piecesValue
     * @return int
     */
    static public function getMinPieces($goods)
    {
        $pieces = $goods instanceof Goods ? $goods->goodsPieces : $goods;
        return ($pieces->pieces_level_3 || $pieces->pieces_level_3 === 0 ? $pieces->pieces_level_3 : ($pieces->pieces_level_2 || $pieces->pieces_level_2 === 0 ? $pieces->pieces_level_2 : $pieces->pieces_level_1));
    }

    /**
     * 返回规格字符串
     *
     * @param \App\Models\Goods $goods
     * @param string $piecesValue
     * @return string
     */
    static public function getPiecesSystem2(Goods $goods, $piecesValue = '')
    {
        $pieces = $goods->goodsPieces->toArray();

        if ($piecesValue == $pieces['pieces_level_1']) {
            $system = $goods->goodsPieces->specification . ($pieces['system_2'] == null ? '' : '*' . $pieces['system_2']) . ($pieces['system_1'] == null ? '' : '*' . $pieces['system_1']);
        } else if ($piecesValue == $pieces['pieces_level_2']) {
            $system = $goods->goodsPieces->specification . ($pieces['system_2'] == null ? '' : '*' . $pieces['system_2']);
        } else if ($piecesValue == $pieces['pieces_level_3']) {
            $system = $goods->goodsPieces->specification;
        } else {
            $system = $goods->goodsPieces->specification;
        }
        return $system;
    }


    /**
     * 格式化商品数量
     *
     * @param $pieces
     * @param $goodsPieces ['pieces' =>'num']
     * @return array
     */
    static function formatGoodsPieces($pieces, $goodsPieces)
    {
        $newArray = [];
        if (!is_null($pieces['pieces_level_3']) && $piecesLevel3 = array_get($goodsPieces, $pieces['pieces_level_3'])) {
            $goodsPieces[$pieces['pieces_level_2']] = isset($goodsPieces[$pieces['pieces_level_2']]) ? bcadd($goodsPieces[$pieces['pieces_level_2']],
                bcdiv($piecesLevel3, $pieces['system_2'])) : bcdiv($piecesLevel3, $pieces['system_2']);
            $goodsPieces[$pieces['pieces_level_3']] = bcmod($piecesLevel3, $pieces['system_2']);
        }

        if (!is_null($pieces['pieces_level_2']) && $piecesLevel2 = array_get($goodsPieces, $pieces['pieces_level_2'])) {
            $goodsPieces[$pieces['pieces_level_1']] = isset($goodsPieces[$pieces['pieces_level_1']]) ? bcadd($goodsPieces[$pieces['pieces_level_1']],
                bcdiv($piecesLevel2, $pieces['system_1'])) : bcdiv($piecesLevel2, $pieces['system_1']);
            $goodsPieces[$pieces['pieces_level_2']] = bcmod($piecesLevel2, $pieces['system_1']);
        }
        //单位从大到小
        $newArray[$pieces['pieces_level_1']] = array_get($goodsPieces, $pieces['pieces_level_1'], 0);
        !is_null($pieces['pieces_level_2']) && ($newArray[$pieces['pieces_level_2']] = array_get($goodsPieces,
            $pieces['pieces_level_2'], 0));
        !is_null($pieces['pieces_level_3']) && ($newArray[$pieces['pieces_level_3']] = array_get($goodsPieces,
            $pieces['pieces_level_3'], 0));

        return array_filter($newArray);
    }

    /**
     * 获取最低数量
     *
     * @param $goodsPieces
     * @param $goodsNum     ['pieces' =>'num']
     * @return mixed
     */
    static function getTheLowLevelNum($goodsPieces, $goodsNum)
    {
        $level1 = $goodsPieces['pieces_level_1'];
        $system1 = $goodsPieces['system_1'];
        $level2 = $goodsPieces['pieces_level_2'];
        $system2 = $goodsPieces['system_2'];
        $level3 = $goodsPieces['pieces_level_3'];

        $step = 1;
        $nextLevel = $level1;
        foreach ([$level1, $level2, $level3] as $level) {
            if (is_null($level) || !isset(${'system' . $step})) {
                continue;
            }
            $system = ${'system' . $step};
            $nextLevel = ${'level' . ($step + 1)};
            if ($item = array_get($goodsNum, $level)) {
                $goodsNum[$nextLevel] = isset($goodsNum[$nextLevel]) ? $goodsNum[$nextLevel] + $system * $item : $system * $item;
            }

            $step++;
        }
        return array_get($goodsNum, $nextLevel, 0);
    }

}