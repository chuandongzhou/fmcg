<?php

namespace App\Http\Controllers\Index;

use App\Models\Shop;
use App\Services\AddressService;
use App\Services\CategoryService;
use App\Services\GoodsService;
use App\Services\ShopService;
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
     * 店铺列表
     *
     * @param \Illuminate\Http\Request $request
     * @param string $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $sort = '')
    {
        $user = auth()->user();
        $gets = $request->all();
        $type = isset($gets['type']) ? $gets['type'] : 'supplier';
        $userTypes = cons('user.type');
        $typeId = array_get($userTypes, $type, last($userTypes));
        //供应商暂时与批发商一致
        $userType = $user->type <= $userTypes['wholesaler'] ? $user->type : $userTypes['wholesaler'];

        $shops = Shop::ofUser($userType, $typeId)->with('logo', 'shopAddress');

        $shopSorts = cons('shop.sort');

        if (in_array($sort, $shopSorts)) {
            $sortName = 'Of' . $sort;
            $shops = $shops->$sortName();
        }

        //配送区域
        $addressData = (new AddressService)->getAddressData();
        $data = array_merge($gets, array_except($addressData, 'address_name'));
        $shops = $shops->OfDeliveryArea(array_filter($data))->with([
            'goods' => function ($query) {
                $query->active()->ofNew();
            }
        ]);
        if (isset($gets['name'])) {
            $shops = $shops->where('name', 'like', '%' . $gets['name'] . '%');
        }
        return view('index.shop.index',
            ['shops' => $shops->paginate(8), 'sort' => $sort, 'get' => $data, 'type' => $type]);
    }

    /**
     * 店铺所有商品页
     *
     * @param $shop
     * @param $sort
     * @return \Illuminate\View\View
     */
    public function shop($shop, $sort = '')
    {
        $user = auth()->user();
        if (Gate::denies('validate-allow', $shop)) {
            return redirect()->back();
        }
        $isLike = $user->likeShops()->where('shop_id', $shop->id)->pluck('id');

        $goods = $shop->goods()->active();

        if (in_array($sort, cons('goods.sort'))) {
            $goods = $sort == 'price' ? $goods->{'Of' . ucfirst($sort)}($shop->user_id) : $goods->{'Of' . ucfirst($sort)}();
        } else {
            $goods = $goods->OfCommonSort()->orderBy('id', 'DESC');
        }
        $goods = $goods->paginate();
        return view('index.shop.shop', [
            'shop' => $shop,
            'goods' => $goods,
            'sort' => $sort,
            'isLike' => $isLike,
        ]);
    }

    /**
     * 店铺首页
     *
     * @param $shop
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail($shop)
    {
        if (Gate::denies('validate-allow', $shop)) {
            return back()->withInput();
        }
        $shop->adverts = $shop->shopHomeAdverts()->with('image')->active()->get();

        $isLike = auth()->user()->likeShops()->where('shop_id', $shop->id)->first();
        $hotGoods = $shop->goods()->active()->ofHot()->take(10)->get();
        $recommendGoods = $shop->recommendGoods()->get();
        return view('index.shop.detail',
            [
                'shop' => $shop,
                'isLike' => $isLike,
                'hotGoods' => $hotGoods,
                'recommendGoods' => $recommendGoods
            ]);
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
        $addressData = (new AddressService)->getAddressData();
        $data = array_merge($data, array_except($addressData, 'address_name'));
        $result = GoodsService::getShopGoods($shop, $data);
        $goods = $result['goods']->active()->hasPrice()->orderBy('id', 'DESC')->paginate();
        $isLike = auth()->user()->likeShops()->where('shop_id', $shop->id)->first();
        $cateId = isset($data['category_id']) ? $data['category_id'] : -1;
        $categories = CategoryService::formatShopGoodsCate($shop, $cateId);
        $shop->load('goods');

        return view('index.shop.search',
            [
                'shop' => $shop,
                'goods' => $goods,
                'categories' => $categories,
                'attrs' => $result['attrs'],
                'searched' => $result['searched'],
                'moreAttr' => $result['moreAttr'],
                'isLike' => $isLike,
                'get' => $gets,
                'data' => $data,
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
