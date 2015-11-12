<?php

namespace App\Http\Controllers\Index;

use App\Models\Goods;
use App\Models\Shop;
use App\Services\GoodsService;
use DB;
use Gate;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('supplier', ['only' => ['index', 'search']]);
    }

    /**
     * 店铺
     *
     * @param \Illuminate\Http\Request $request
     * @param string $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $sort = '')
    {
        $user = auth()->user();
        $shops = Shop::whereHas('user', function ($q) use ($user) {
            $q->where('type', '>', $user->type);
        })->with('images', 'logo');

        $shopSorts = cons('shop.sort');

        if (in_array($sort, $shopSorts)) {
            $sortName = 'Of' . $sort;
            $shops = $shops->$sortName();
        }

        //配送区域
        $address = $request->except('name');
        if (!empty($address)) {
            $shops = $shops->OfDeliveryArea($address);
        }
        // 名称
        $name = $request->input('name');

        if ($name) {
            $shops = $shops->where('name', 'like', '%' . $name . '%');
        }


        return view('index.shop.index', ['shops' => $shops->paginate(), 'sort' => $sort, 'address' => $address]);
    }

    /**
     * 店铺首页
     *
     * @param $shop
     * @param $sort
     * @return \Illuminate\View\View
     */
    public function shop($shop, $sort = '')
    {
        if (Gate::denies('validate-allow', $shop)) {
            return redirect()->back();
        }
        $shop->load('images');

        $isLike = auth()->user()->likeShops()->where('shop_id', $shop->id)->pluck('id');
        $map = ['shop_id' => $shop->id];

        $goods = Goods::where($map)->with('images.image');
        if (in_array($sort, cons('goods.sort'))) {
            $goods = $goods->{'Of'.ucfirst($sort)}();
        }
        $goods = $goods->paginate();
        $url = Gate::denies('validate-shop', $shop) ? 'goods' : 'my-goods';

        return view('index.shop.shop', [
            'shop' => $shop,
            'goods' => $goods,
            'sort' => $sort,
            'isLike' => $isLike,
            'url' => $url
        ]);
    }


    /**
     * 店铺详情
     *
     * @param $shop
     * @return \Illuminate\View\View
     */
    public function detail($shop)
    {
        if (Gate::denies('validate-allow', $shop)) {
            return back()->withInput();
        }
        $coordinate = $shop->deliveryArea->each(function ($area) {
            $area->coordinate;
        });

        $isLike = auth()->user()->likeShops()->where('shop_id', $shop->id)->first();

        return view('index.shop.detail', ['shop' => $shop, 'isLike' => $isLike, 'coordinates' => $coordinate->toJson()]);
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

        $goods = $shop->goods()->with('images');

        $result = GoodsService::getGoodsBySearch($data, $goods);
        $isLike = auth()->user()->likeShops()->where('shop_id', $shop->id)->first();
        return view('index.shop.search',
            [
                'shop' => $shop,
                'goods' => $goods->paginate(),
                'categories' => $result['categories'],
                'attrs' => $result['attrs'],
                'searched' => $result['searched'],
                'moreAttr' => $result['moreAttr'],
                'isLike' => $isLike,
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
