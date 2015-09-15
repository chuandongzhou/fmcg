<?php

namespace App\Http\Controllers\Index;

use App\Models\Category;
use App\Models\Goods;
use App\Services\AttrService;
use DB;
use Illuminate\Http\Request;


class ShopController extends Controller
{

    protected $sort = [
        'name',
        'price',
        'new'
    ];


    /**
     * 店铺首页
     *
     * @param $shop
     * @param $type
     * @return \Illuminate\View\View
     */
    public function index($shop, $type = '')
    {
        $map = ['shop_id' => $shop->id];

        $goods = Goods::where($map);
        if (in_array($type, $this->sort)) {
            $goods = $goods->$type();
        }
        $goods = $goods->paginate();
        return view('index.shop.index',
            ['shop' => $shop, 'goods' => $goods, 'type' => $type]);
    }


    /**
     * 店铺详情
     *
     * @param $shop
     * @return \Illuminate\View\View
     */
    public function detail($shop)
    {
        return view('index.shop.detail', ['shop' => $shop]);
    }

    /**
     * 商家商品搜索
     *
     * @param \Illuminate\Http\Request $request
     * @param $shop
     * @return \Illuminate\View\View
     */
    public function search(Request $request, $shop)
    {
        $gets = $request->all();
        $data = array_filter($this->_formatGet($gets));
        $attrs = [];

        $categories = Category::orderBy('level', 'asc')->select('name', 'level', 'id')->get();
        $goods = $shop->goods()->with('images');
        //排序
        if (isset($data['sort']) && in_array($data['sort'], $this->sort)) {
            $goods = $goods->{'Order' . ucfirst($data['sort'])}();
        }
        // 省市县
        if (isset($data['province_id'])) {
            $goods = $goods->OfDeliveryArea($data);
        }
        // 名称
        if (isset($data['name'])) {
            $goods = $goods->where('name', 'like', '%' . $data['name'] . '%');
        }
        if (isset($data['cate'])) {
            //分类最高位为层级 后面为categoryId
            $level = substr($data['cate'], 0, 1);
            $categoryId = substr($data['cate'], 1);
            $attrs = (new AttrService([]))->getAttrByCategoryId($categoryId);
            $goods = $goods->OfCategory($categoryId, $level);
        }

        // 标签
        if (isset($data['attr'])) {
            $goods = $goods->OfAttr($data['attr']);
        }
        return view('index.shop.search',
            [
                'shop' => $shop,
                'goods' => $goods->paginate(),
                'categories' => $categories,
                'attrs' => $attrs,
                'get' => $gets,
                'data' => $data
            ]);
    }

    /**
     * 格式化查询每件
     *
     * @param $get
     * @return array
     */
    private function _formatGet($get)
    {
        $data = [];
        foreach ($get as $key => $val) {
            if (starts_with($key, 'attr_')) {
                $pid = explode('_', $key)[1];
                $data['attr'][$pid] = $val;
            } else {
                $data[$key] = $val;
            }
        }
        return $data;
    }
}
