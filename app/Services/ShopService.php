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
        if (Cache::has($cacheKey)) {
            $shopColumns = Cache::get($cacheKey);
        } else {
            //商家
            $shopColumns = HomeColumn::where('type', $columnTypes['shop'])->get();

            foreach ($shopColumns as $shopColumn) {
                $shops = Shop::whereIn('id', $shopColumn->id_list)->OfUser($type)->with('images',
                    'logo')->get()->each(function ($shop) {
                    $shop->setAppends(['image_url', 'logo', 'user']);
                });
                $columnShopsCount = $shops->count();
                if ($columnShopsCount < 10) {
                    $columnShopsIds = $shops->pluck('id')->toArray();
                    $ShopsBySort = Shop::whereNotIn('id', $columnShopsIds)
                        ->OfUser($type)
                        ->with('images', 'logo')
                        ->{'Of' . ucfirst(camel_case($shopColumn->sort))}()
                        ->take(10 - $columnShopsCount)
                        ->get()->each(function ($shop) {
                            $shop->setAppends(['image_url', 'logo', 'user']);
                        });
                    $shops = $shops->merge($ShopsBySort);
                }
                $shopColumn->shops = $shops;
            }
            Cache::put($cacheKey, $shopColumns, $homeColumnShopConf['cache']['expire']);
        }
        return $shopColumns;
    }
}