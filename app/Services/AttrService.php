<?php

namespace App\Services;

use App\Models\Attr;
use App\Models\Category;
use App\Models\Goods;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class AttrService
{

    protected $attrs = [];

    public function __construct($attrs = [])
    {
        $this->attrs = $attrs;
        return $this;
    }

    /**
     * @param array $attrs
     * @param bool|false $onlyName
     * @return array
     */
    public function format($attrs = [], $onlyName = false)
    {
        $attrs = empty($attrs) ? $this->attrs : $attrs;

        if (empty($attrs)) {
            return [];
        }

        $collect = collect($attrs);
        $attrList = array_values($collect->where('pid', 0)->all());
        $nameArray = [];
        foreach ($attrList as $key => $level) {
            foreach ($attrs as $attr) {
                if ($level['attr_id'] == $attr['pid']) {
                    $onlyName ? $nameArray[$attrList[$key]['name']] = $attr['name'] : $attrList[$key]['child'][$attr['attr_id']] = $attr;
                }
            }
        }
        return $onlyName ? $nameArray : $attrList;
    }

    /**
     *
     *
     * @param $category
     * @return array
     */
    public function getAttrByCategoryId($category)
    {
        if (is_numeric($category)) {
            $category = Category::find($category);
            if (is_null($category)) {
                return [];
            }
        }else{
            return [];
        }

        $attrs = $category->attrs()->select(['attr_id', 'pid', 'name'])->get()->toArray();
        return $this->format($attrs);
    }

    /**
     * 根据商品查出标签
     *
     * @param $goods
     * @param bool $default
     * @return array
     */
    public function getAttrByGoods($goods, $default = false)
    {
        $attrGoods = [];
        if (is_numeric($goods)) {
            $goods = Goods::find($goods);
        } else {
            if (!$goods instanceof Goods) {
                return [];
            }
        }
        $attrDefault = cons('attr.default');
        foreach ($goods->attr as $key => $attr) {
            $pivot = $attr->pivot->toArray();
            if ($default) {
                if (in_array($pivot['attr_pid'], $attrDefault)) {
                    $attrGoods[] = $pivot;
                }
            } else {
                $attrGoods[] = $pivot;
            }

        }
        $attrIds = array_merge(array_pluck($attrGoods, 'attr_id'), array_pluck($attrGoods, 'attr_pid'));
        $attrResults = Attr::select(['attr_id', 'pid', 'name'])
            ->where('category_id', $goods->category_id)
            ->whereIn('attr_id', $attrIds)
            ->get()
            ->toArray();

        $attrResults = $this->format($attrResults, true);
        return $attrResults;
    }
}