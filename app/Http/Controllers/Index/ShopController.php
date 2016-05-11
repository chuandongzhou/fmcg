<?php

namespace App\Http\Controllers\Index;

use App\Models\Advert;
use App\Models\Shop;
use App\Services\AddressService;
use App\Services\CategoryService;
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
        $gets = $request->all();
        $type = isset($gets['type']) ? $gets['type'] : 'supplier';
        $userTypes = cons('user.type');
        $typeId = array_get($userTypes, $type, last($userTypes));
        //供应商暂时与批发商一致
        $userType = $user->type <= $userTypes['wholesaler'] ? $user->type : $userTypes['wholesaler'];

        $shops = Shop::OfUser($userType, $typeId)->with('logo', 'shopAddress');

        $shopSorts = cons('shop.sort');

        if (in_array($sort, $shopSorts)) {
            $sortName = 'Of' . $sort;
            $shops = $shops->$sortName();
        }

        //配送区域
        $addressData = (new AddressService)->getAddressData();
        $data = array_merge($gets, array_except($addressData, 'address_name'));
        $shops = $shops->OfDeliveryArea(array_filter($data));

        if (isset($gets['name'])) {
            $shops = $shops->where('name', 'like', '%' . $gets['name'] . '%');
        }
        return view('index.shop.index',
            ['shops' => $shops->paginate(16), 'sort' => $sort, 'get' => $data, 'type' => $type]);
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
        $user = auth()->user();
        if (Gate::denies('validate-allow', $shop)) {
            return redirect()->back();
        }
        $shop->load('images');

        if ($shop->images->isEmpty()) {
            $advert = $this->_getAdvertFirstImage();
            $shop->images[0] = $advert->image;
        }
        $isLike = $user->likeShops()->where('shop_id', $shop->id)->pluck('id');

        $goods = $shop->goods()->active()->with('images.image');
        if (in_array($sort, cons('goods.sort'))) {
            $goods = $goods->{'Of' . ucfirst($sort)}();
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
        $addressData = (new AddressService)->getAddressData();
        $data = array_merge($data, array_except($addressData, 'address_name'));
        $result = GoodsService::getShopGoods($shop, $data);
        $goods = $result['goods']->orderBy('id', 'DESC')->paginate();
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
