<?php

namespace App\Services;

use App\Models\Attr;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class AttrService
{

    protected $attrs = [];

    public function __construct($attrs)
    {
        $this->attrs = $attrs;
        return $this;
    }

    /**
     * 格式化标签
     *
     * @return array
     */
    public function format($attrs = [])
    {
        $attrs = empty($attrs) ? $this->attrs : $attrs;

        if (empty($attrs)) {
            return [];
        }

        $collect = collect($attrs);
        $attrList = array_values($collect->where('pid', 0)->all());
        foreach ($attrList as $key => $level) {
            foreach ($attrs as $attr) {
                if ($level['id'] == $attr['pid']) {
                    $attrList[$key]['child'][] = $attr;
                }
            }
        }
        return $attrList;
    }

    /**
     *
     *
     * @param $categoryId
     * @return array
     */
    public function getAttrByCategoryId($categoryId)
    {
        $attrs = Attr::where('category_id', $categoryId)->select(['id', 'pid', 'name'])->get()->toArray();
        return  $this->format($attrs);
    }
}