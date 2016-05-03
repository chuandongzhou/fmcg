<?php

namespace App\Services;

use App\Models\Shop;
use App\Models\ShopColumn;
use Illuminate\Support\Facades\Cache;
use QrCode;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class ShopService
{
    static function getShopColumn()
    {
        $type = auth()->user()->type;

        $homeColumnShopConf = cons('home_column.shop');


        $shopColumns = [];

        $addressData = (new AddressService)->getAddressData();
        $data = array_except($addressData, 'address_name');

        $cacheKey = $homeColumnShopConf['cache']['pre_name'] . $type . ':' . $data['province_id'] . ':' . $data['city_id'];

        if (Cache::has($cacheKey)) {
            $shopColumns = Cache::get($cacheKey);
        } else {
            //商家
            $shopColumns = ShopColumn::get();

            foreach ($shopColumns as $shopColumn) {
                $shops = Shop::whereIn('id', $shopColumn->id_list)
                    ->OfUser($type)
                    ->OfDeliveryArea(array_filter($data))
                    ->with('images', 'logo', 'user')
                    ->get()
                    ->each(function ($shop) {
                        $shop->setAppends(['image_url', 'logo']);
                    });
                $columnShopsCount = $shops->count();
                if ($columnShopsCount < 10) {
                    $columnShopsIds = $shops->pluck('id')->toArray();
                    $ShopsBySort = Shop::whereNotIn('id', $columnShopsIds)
                        ->OfUser($type)
                        ->with('images', 'logo', 'user')
                        ->{'Of' . ucfirst(camel_case($shopColumn->sort))}()
                        ->OfDeliveryArea(array_filter($data))
                        ->take(10 - $columnShopsCount)
                        ->get()->each(function ($shop) {
                            $shop->setAppends(['image_url', 'logo']);
                        });
                    $shops = $shops->merge($ShopsBySort);
                }
                $shopColumn->shops = $shops;
            }
            Cache::put($cacheKey, $shopColumns, $homeColumnShopConf['cache']['expire']);
        }
        return $shopColumns;
    }


    /**
     * 获取店铺二维码头像
     *
     * @param int $uid
     * @param $size
     * @return string
     */
    static function qrcode($uid = 0, $size = null)
    {
        $qrcodePath = config('path.upload_qrcode');
        $relatePath = str_replace(public_path(), '', $qrcodePath);
        $qrcodeSize = is_null($size) ? cons('shop.qrcode_size') : $size;
        // 处理分割后的ID
        $path = implode('/', divide_uid($uid, "/{$qrcodeSize}.png"));

        if (!is_file($qrcodePath . $path)) {
            @mkdir(dirname($qrcodePath . $path), 0777, true);
            QrCode::format('png')->size($qrcodeSize)->generate(url('shop/' . $uid), $qrcodePath . $path);
        }
        // 处理缓存
        $mtime = @filemtime($qrcodePath . $path);
        if (false !== $mtime) {
            return asset($relatePath . $path) . '?' . $mtime;
        }
        return asset($relatePath . $path);
    }

}