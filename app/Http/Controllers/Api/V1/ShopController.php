<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/18
 * Time: 11:35
 */
namespace App\Http\Controllers\Api\V1;


use App\Models\Goods;
use App\Models\Shop;
use App\Services\ShopService;
use Gate;

class ShopController extends Controller
{
    /**
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function shops()
    {
        // dd(ShopService::getShopColumn());
        return $this->success(['shopColumn' => ShopService::getShopColumn()]);
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
        $likeShops = auth()->user()->likeShops()->where('shop_id', $shop->id)->first();
        $isLike = !is_null($likeShops);
        return $this->success([
            'shop' => $shop,
            'isLike' => $isLike
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
        $shopExtend = [];
        $shopExtend['license_url'] = $shop->license_url;
        $shopExtend['business_license_url'] = $shop->business_license_url;
        $shopExtend['agency_contract_url'] = $shop->Agency_contract_url;
        $shopExtend['images_url'] = $shop->images_url;
        $shopExtend['delivery_area'] = $shop->deliveryArea;
        $shopExtend['address'] = $shop->shopAddress;

        return $this->success(['extend' => $shopExtend]);
    }

    public function goods($shop)
    {
        if (Gate::denies('validate-allow', $shop)) {
            return $this->success(
                [
                    'shop' => [],
                    'goods' => [],
                    'isLike' => []
                ]);
        }

        $goods = $shop->goods()->select([
            'id',
            'name',
            'sales_volume',
            'price_retailer',
            'price_wholesaler',
            'is_new',
            'is_promotion',
            'is_out',
        ])->paginate()->toArray();
        return $this->success(['goods' => $goods]);
    }
}