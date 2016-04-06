<?php

namespace App\Http\Controllers\Index;

use App\Models\Advert;
use App\Models\Goods;
use App\Models\Shop;
use App\Services\GoodsService;
use Carbon\Carbon;
use DB;
use Gate;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct()
    {
        //$this->middleware('supplier', ['only' => ['index']]);
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
        $type = (string)$request->input('type');

        $userTypes = cons('user.type');
        $typeId = array_get($userTypes, $type, last($userTypes));
        //供应商暂时与批发商一致
        $userType = $user->type <= $userTypes['wholesaler'] ? $user->type : $userTypes['wholesaler'];

        $shops = Shop::OfUser($userType, $typeId)->with('images', 'shopAddress');

        $shopSorts = cons('shop.sort');

        if (in_array($sort, $shopSorts)) {
            $sortName = 'Of' . $sort;
            $shops = $shops->$sortName();
        }

        //配送区域
        $data = $request->except('name');

        $data['province_id'] = $request->cookie('province_id') ? $request->cookie('province_id') : cons('address.default_province');

        if (!empty($data)) {
            $shops = $shops->OfDeliveryArea($data);
        }
        // 名称
        $name = $request->input('name');

        if ($name) {
            $shops = $shops->where('name', 'like', '%' . $name . '%');
        }
        return view('index.shop.index',
            ['shops' => $shops->paginate(), 'sort' => $sort, 'address' => $data, 'type' => $type]);
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

        if ($shop->images->isEmpty()) {
            $advert = $this->_getAdvertFirstImage();
            $shop->images[0] = $advert->image;
        }
        $isLike = auth()->user()->likeShops()->where('shop_id', $shop->id)->pluck('id');
        $map = ['shop_id' => $shop->id];

        $goods = Goods::active()->where($map)->with('images.image');
        if (in_array($sort, cons('goods.sort'))) {
            $goods = $goods->{'Of' . ucfirst($sort)}();
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

        if ($shop->images->isEmpty()) {
            $advert = $this->_getAdvertFirstImage();
            $shop->images[0] = $advert->image;
        }

     /*   $coordinate = $shop->deliveryArea->each(function ($area) {
            $area->coordinate;
        });*/

        $isLike = auth()->user()->likeShops()->where('shop_id', $shop->id)->first();

        return view('index.shop.detail',
            ['shop' => $shop, 'isLike' => $isLike/*, 'coordinates' => $coordinate*/]);
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
        $goods = $shop->goods()->ofStatus(cons('goods.status.on'))->with('images');
        $data['province_id'] = request()->cookie('province_id') ? request()->cookie('province_id') : cons('address.default_province');
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

    /**
     * 商店图片为空时获取首页广告的第一张图
     *
     * @return mixed
     */
    private function _getAdvertFirstImage()
    {
        $nowTime = Carbon::now();
        $advert = Advert::with('image')->where('type', cons('advert.type.index'))->OfTime($nowTime)->first();
        return $advert->image ? $advert : new Advert;
    }
}
