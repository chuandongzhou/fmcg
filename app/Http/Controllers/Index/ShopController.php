<?php

namespace App\Http\Controllers\Index;

use App\Models\Goods;
use App\Models\Shop;
use DB;


class ShopController extends Controller
{

    protected $searchType = [
        'hot',
        'new',
        'promotion'
    ];

    /**
     * 首页
     *
     * @return \Illuminate\View\View
     */
    public function getDetail($cateStr = 'all', $type = '')
    {
        $shop = auth()->user()->shop()->with('images')->first();

        $map = ['shop_id' =>$shop->id];
        $goods = new \stdClass();

        /**
         * $category  第一个为层级  后面为categoryId
         */
        if (is_numeric($cateStr) && strlen($cateStr) > 1) {
            $level = substr($cateStr, 0, 1);
            $categoryId = substr($cateStr, 1);
            $goods = Goods::where('cate_level_'.$level, $categoryId);
        }

        $goods = $goods instanceof \stdClass ? Goods::where($map) : $goods->where($map);
        if (in_array($type, $this->searchType)) {
            $goods = $goods->$type();
        }
        $goods = $goods->paginate();
        return view('index.shop.detail',
            ['shop' => $shop, 'goods' => $goods, 'type' => $type, 'categoryId' => $cateStr]);
    }

    public function getIndex()
    {

    }
}
