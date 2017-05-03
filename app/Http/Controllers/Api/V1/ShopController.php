<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/18
 * Time: 11:35
 */
namespace App\Http\Controllers\Api\V1;


use App\Models\Shop;
use App\Services\AddressService;
use App\Services\CategoryService;
use App\Services\GoodsService;
use App\Services\ShopService;
use Gate;
use DB;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function shops()
    {
        return $this->success(['shopColumn' => (new ShopService())->getShopColumn()]);
    }

    /**
     * 查询店铺按距离
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function allShops(Request $request)
    {
        $xLng = $request->input('x_lng', 0);  //经度
        $yLat = $request->input('y_lat', 0);  //纬度

        $type = auth()->user() ? auth()->user()->type : cons('user.type.retailer');
        $addressData = (new AddressService())->getAddressData();
        $data = array_except($addressData, 'address_name');

        $shops = Shop::select(DB::raw('(6370996.81 * ACOS( COS(' . $yLat . ' * PI() / 180)
             * COS(y_lat * PI() / 180) * COS(' . $xLng . ' * PI() / 180 - x_lng * PI() / 180 )
              + SIN(' . $yLat . ' * PI() / 180) * SIN(y_lat * PI() / 180)  ) ) distance'), 'id',
            'name', 'min_money', 'user_id', 'contact_person', 'contact_info', 'x_lng', 'y_lat')
            ->with('logo', 'shopAddress', 'user')
            ->OfUser($type)->OfDeliveryArea($data)->OfName($request->input('name'))->orderBy('distance')->paginate();
        $shops->each(function ($item) {
            $item->setAppends(['goods_count', 'sales_volume', 'three_goods'])->setHidden(['goods']);
        });
        return $this->success($shops->toArray());
    }

    /**
     * 获取店铺分类
     *
     * @param $shop
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function category($shop)
    {
        $categories = CategoryService::formatShopGoodsCate($shop);
        return $this->success(['categories' => $categories]);
    }

    /**
     * 店铺详情
     *
     * @param $shop
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function detail($shop)
    {

        if (Gate::denies('validate-allow', $shop)) {
            return $this->success(
                [
                    'shop' => [],
                    'goods' => [],
                    'isLike' => []
                ]);
        }

        $isLike = auth()->user()->likeShops()->where('shop_id', $shop->id)->pluck('id');
        $shop->is_like = $isLike ? true : false;
        $shop->load(['deliveryArea', 'shopAddress']);
        $shop->setAppends(['goods_count', 'sales_volume'])->setHidden(['goods']);
        return $this->success([
            'shop' => $shop->toArray()
        ]);
    }

    /**
     * 获取店铺详细地址
     *
     * @param $shop
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function extend($shop)
    {
        $shopExtend['license_url'] = $shop->license_url;
        $shopExtend['business_license_url'] = $shop->business_license_url;
        $shopExtend['agency_contract_url'] = $shop->Agency_contract_url;
        $shopExtend['images_url'] = $shop->images_url;
        $shopExtend['delivery_area'] = $shop->deliveryArea;
        $shopExtend['address'] = $shop->shopAddress;

        return $this->success(['extend' => $shopExtend]);
    }


    /**
     * 根据店铺id获取店铺
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\WeiHeng\Responses\Apiv1Response
     */
    public function getShopsByIds(Request $request)
    {
        $shopIds = $request->input('data');
        if (empty($shopIds)) {
            return [];
        }
        $shops = Shop::with(['logo', 'user'])->whereIn('id', $shopIds)->get(['name', 'id', 'user_id'])->each(function (
            $shop
        ) {
            $shop->setAppends(['logo_url']);
        });
        return $this->success(['shops' => $shops->keyBy('id')]);
    }

    /**
     * 获取店铺商品
     *
     * @param \Illuminate\Http\Request $request
     * @param $shop
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function goods(Request $request, $shop)
    {
        if (Gate::denies('validate-allow', $shop)) {
            return $this->success(
                [
                    'shop' => new Shop,
                    'goods' => [],
                    'isLike' => false
                ]);
        }
        $data = $request->all();
        $result = GoodsService::getShopGoods($shop, $data);
        $goods = $result['goods']->active()->orderBy('id', 'DESC')->paginate()->toArray();
        return $this->success(['goods' => $goods]);
    }

    /**
     * 获取店铺广告
     *
     * @param $shop
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function adverts($shop)
    {
        $adverts = $shop->adverts()->OfTime()->get()->each(function ($advert) {
            $advert->setAppends(['goods_id', 'image_url']);
        });
        return $this->success(['adverts' => $adverts]);
    }


}