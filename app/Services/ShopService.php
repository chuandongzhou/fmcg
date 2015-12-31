<?php

namespace App\Services;

use App\Models\HomeColumn;
use App\Models\Shop;
use Illuminate\Support\Facades\Cache;

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

        $columnTypes = cons('home_column.type');
        $homeColumnShopConf = cons('home_column.shop');
        $cacheKey = $homeColumnShopConf['cache']['pre_name'] . $type;

        $shopColumns = [];
        $provinceId = request()->cookie('province_id') ? request()->cookie('province_id') : cons('location.default_province');
        if (Cache::has($cacheKey) && Cache::get($cacheKey)[0]->province_id == $provinceId) {
            $shopColumns = Cache::get($cacheKey);
        } else {
            //商家
            $shopColumns = HomeColumn::where('type', $columnTypes['shop'])->get();

            foreach ($shopColumns as $shopColumn) {
                $shops = Shop::whereIn('id', $shopColumn->id_list)
                    ->OfUser($type)
                    ->OfDeliveryArea(['province_id' => $provinceId])
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
                        ->OfDeliveryArea(['province_id' => $provinceId])
                        ->take(10 - $columnShopsCount)
                        ->get()->each(function ($shop) {
                            $shop->setAppends(['image_url', 'logo']);
                        });
                    $shops = $shops->merge($ShopsBySort);
                }
                $shopColumn->shops = $shops;
                $shopColumn->province_id = $provinceId;
            }
            Cache::put($cacheKey, $shopColumns, $homeColumnShopConf['cache']['expire']);
        }
        return $shopColumns;
    }
}