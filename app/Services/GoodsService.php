<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Goods;
use App\Models\HomeColumn;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

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
     * @param $goods
     * @param bool|true $isWeb
     * @return array
     */
    static function getGoodsBySearch($data, $goods, $isWeb = true)
    {
        //排序
        if (isset($data['sort']) && in_array(strtolower($data['sort']), cons('goods.sort'))) {
            $goods->{'Of' . ucfirst($data['sort'])}();
        }
        // 省市县
        if (isset($data['province_id'])) {
            $goods->OfDeliveryArea($data);
        }

        $attrs = [];
        $resultCategories = [];
        $categories = Category::orderBy('level', 'asc')->with('icon')->select('name', 'level', 'id',
            'pid')->get()->toArray();
        if (isset($data['category_id'])) {
            //分类最高位为层级 后面为categoryId
            $level = substr($data['category_id'], 0, 1);
            $categoryId = substr($data['category_id'], 1);
            $resultCategories = CategoryService::formatCategoryForSearch($categories, $categoryId);

            $attrs = (new AttrService([]))->getAttrByCategoryId($categoryId);
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
            $expiresAt = Carbon::now()->addDays(1);
            Cache::put($cacheKey, $keywords, $expiresAt);

            $goods->where('name', 'like', '%' . $data['name'] . '%')->get();

            $categoryIds = array_unique($goods->lists('cate_level_2')->toArray());
            $categories = array_filter($categories, function ($val) use ($categoryIds) {
                return in_array($val['id'], $categoryIds);
            });
        }


        $defaultAttrName = cons()->valueLang('attr.default');

        $searched = []; //保存已搜索的标签
        $moreAttr = []; //保存扩展的标签

        if ($isWeb) {
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
     * 获取首页商品栏目
     *
     * @return mixed
     */
    static function getGoodsColumn()
    {
        $type = auth()->user()->type;

        $columnTypes = cons('home_column.type');
        $homeColumnGoodsConf = cons('home_column.goods');
        $cacheKey = $homeColumnGoodsConf['cache']['pre_name'] . $type;

        $goodsColumns = [];
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
                'is_new',
                'is_out',
                'is_promotion',
                'sales_volume'
            ];
            $displayCount = $homeColumnGoodsConf['count']; //显示条数
            foreach ($goodsColumns as $goodsColumn) {
                $goods = Goods::whereIn('id', $goodsColumn->id_list)
                    ->where('user_type', '>', $type)
                    ->ofStatus(cons('goods.status.on'))
                    ->with('images')
                    ->select($goodsFields)
                    ->get()->each(function ($goods) {
                        $goods->setAppends(['image_url']);
                    });
                $columnGoodsCount = $goods->count();

                if ($columnGoodsCount < $displayCount) {
                    $columnGoodsIds = $goods->pluck('id')->toArray();
                    $goodsBySort = Goods::whereNotIn('id', $columnGoodsIds)
                        ->where('user_type', '>', $type)
                        ->ofStatus(cons('goods.status.on'))
                        ->{'Of' . ucfirst(camel_case($goodsColumn->sort))}()
                        ->with('images.image')
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

}