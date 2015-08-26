<?php

namespace App\Http\Controllers\Index;

use App\Models\Category;
use App\Models\Goods;
use App\Models\Shop;
use App\Services\CategoryService;
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
    public function getDetail($categoryId = 'all', $type = '')
    {
        //TODO: shop_id 修改
        $shop = Shop::where('user_id', 1)->first(); //商店详情
        $map = ['shop_id' => 1];
        $goods = new \stdClass();

        if (is_numeric($categoryId) && $categoryId > 0) {
            $ids = CategoryService::getChildsId(Category::select(['id', 'pid'])->get(), $categoryId);
            $goods = Goods::whereIn('category_id', $ids);
        }

        $goods = $goods instanceof \stdClass ? Goods::where($map) : $goods->where($map);
        if (in_array($type, $this->searchType)) {
            $goods = $goods->$type();
        }
        $goods = $goods->paginate();
        return view('index.shop.detail',
            ['shop' => $shop, 'goods' => $goods, 'type' => $type, 'categoryId' => $categoryId]);
    }

    public function getIndex()
    {

    }
}
