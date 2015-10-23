<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Goods;
use App\Models\HomeColumn;
use App\Models\Shop;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class ShopService
{
    static function getShopColumn(){
        $type = auth()->user()->type;

        $columnTypes = cons('home_column.type');

        //商家
        $shopColumns = HomeColumn::where('type', $columnTypes['shop'])->get();

        foreach ($shopColumns as $shopColumn) {
            $shops = Shop::whereIn('id', $shopColumn->id_list)->whereHas('user', function ($q) use ($type) {
                $q->where('type', '>', $type);
            }
            )->with('images')->get();
            $columnShopsCount = $shops->count();
            if ($columnShopsCount < 10) {
                $columnShopsIds = $shops->pluck('id')->toArray();
                $ShopsBySort = Shop::whereNotIn('id', $columnShopsIds)
                    ->whereHas('user', function ($q) use ($type) {
                        $q->where('type', '>', $type);
                    })
                    ->with('images')
                    ->{'Of' . ucfirst(camel_case($shopColumn->sort))}()
                    ->take(10 - $columnShopsCount)
                    ->get();
                $shops = $shops->merge($ShopsBySort);
            }
            $shopColumn->shops = $shops;
        }
        return $shopColumns;
    }
}