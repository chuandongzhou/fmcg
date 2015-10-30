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
use DB;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function shops()
    {
        return $this->success(['shopColumn' => ShopService::getShopColumn()]);
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
        $userType = auth()->user()->type;

            $shops = Shop::select(DB::raw('(6370996.81 * ACOS( COS(' . $yLat . ' * PI() / 180)
             * COS(y_lat * PI() / 180) * COS(' . $xLng . ' * PI() / 180 - x_lng * PI() / 180 )
              + SIN(' . $yLat . ' * PI() / 180) * SIN(y_lat * PI() / 180)  ) ) distance'), 'id',
            'name', 'min_money', 'contact_info')
            ->with('images', 'logo', 'shopAddress')
            ->whereHas('user', function ($q) use ($userType) {
                $q->where('type', '>', $userType);
            })->orderBy('distance')->paginate();
        return $this->success($shops->toArray());
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
        return $this->success([
            'shop' => $shop
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