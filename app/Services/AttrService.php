<?php

namespace App\Services;
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
     * @return array
     */
    public function format()
    {
        $attrs = $this->attrs;

        if(empty($attrs)){
            return [];
        }

        $collect = collect($attrs);
        $attrList = $collect->where('pid', 0)->all();
        foreach ($attrList as $key => $level) {
            foreach ($attrs as $attr) {
                if ($level['id'] == $attr['pid']) {
                    $attrList[$key]['child'][] = $attr;
                }
            }
        }
        return $attrList;
    }
}