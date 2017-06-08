<?php

namespace App\Http\Controllers\Mobile;

use App\Models\Shop;
use App\Services\AddressService;
use Cache;
use Gate;
use Illuminate\Http\Request;

class ShopController extends Controller
{

    /**
     * 店铺列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\WeiHeng\Responses\IndexResponse
     */
    public function index(Request $request)
    {
        $type = auth()->check() ? auth()->user()->type : cons('user.type.retailer');
        $addressData = (new AddressService())->getAddressData();
        $data = array_except($addressData, 'address_name');

        $shops = Shop::select('id', 'name', 'min_money', 'user_id', 'contact_person', 'contact_info')
            ->with('logo', 'shopAddress', 'user')
            ->OfUser($type)->OfDeliveryArea($data)->OfName($request->input('name'))->orderBy('id')->paginate();
        $shops->each(function ($item) {
            $item->three_goods = $item->goods()->active()->ofNew()->limit(3)->get();
            $item->setAppends(['goods_count', 'sales_volume', 'logo_url', 'user_type'])->setHidden(['goods']);
        });

        if ($request->ajax()) {
            $shops = $shops->toArray();

            return $this->success(compact('shops'));
        }
        return view('mobile.shop.index', compact('shops'));
    }

    /**
     * 店铺详情
     *
     * @param \Illuminate\Http\Request $request
     * @param $shop
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\WeiHeng\Responses\IndexResponse
     */
    public function detail(Request $request, $shop)
    {
        if (Gate::denies('validate-allow', $shop)) {
            return $this->error('店铺不存在');
        }
        $goods = $shop->goods()->active()->hasPrice()->ofCommonSort()->paginate();
        if ($request->ajax()) {
            $goods = $goods->each(function ($item) {
                $item->setAppends(['image_url', 'pieces', 'price']);
            })->toArray();
            return $this->success(compact('goods'));
        }

        return view('mobile.shop.detail', compact('shop', 'goods'));
    }


    /**
     * 优惠券
     *
     * @param $shop
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\WeiHeng\Responses\IndexResponse
     */
    public function coupons($shop)
    {
        if (Gate::denies('validate-allow', $shop)) {
            return $this->error('店铺不存在');
        }
        $coupons = $shop->coupons()->with('shop')->get()->filter(function ($coupon) {
            $coupon->shop->setAppends([]);
            return $coupon->can_receive;
        });

        return view('mobile.shop.coupon', compact('shop', 'coupons'));
    }

    /**
     * 配送区域
     *
     * @param $shop
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function deliveryArea($shop)
    {
        if (Gate::denies('validate-allow', $shop)) {
            return $this->error('店铺不存在');
        }
        $area = $shop->deliveryArea;

        return view('mobile.shop.delivery-area', compact('shop', 'area'));
    }

    /**
     * 店铺二维码
     *
     * @param $shop
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function qrCode($shop)
    {
        if (Gate::denies('validate-allow', $shop)) {
            return $this->error('店铺不存在');
        }
        return view('mobile.shop.qr-code', compact('shop'));
    }

    /**
     * 店铺商品搜索
     *
     * @param \Illuminate\Http\Request $request
     * @param $shop
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\WeiHeng\Responses\IndexResponse
     */
    public function goods(Request $request, $shop)
    {
        if (Gate::denies('validate-allow', $shop)) {
            return $this->error('店铺不存在');
        }
        $name = $request->input('name');

        $goods = $shop->goods()->active()->ofNameOrCode($name)->ofCommonSort()->paginate();

        if ($request->ajax()) {
            $goods = $goods->each(function ($item) {
                $item->setAppends(['image_url', 'pieces', 'price']);
            })->toArray();

            return $this->success(compact('goods'));
        }

        return view('mobile.shop.goods', compact('shop', 'goods', 'name'));

    }
}
