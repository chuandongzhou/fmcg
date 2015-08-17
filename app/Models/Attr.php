<?php

namespace App\Models;


class Attr extends Model
{
    protected $table = 'attr';
    public $timestamp = false;
    protected $fillable = ['name', 'category_id', 'pid', 'status', 'sort', 'is_default'];

    /**
     * 分类表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    /**
     * 格式化标签
     * @param $attrs
     * @return array
     */
    static public function formatAttr($attrs){
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
