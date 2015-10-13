<?php

namespace App\Http\Controllers\Index;

use App\Models\Advert;
use App\Models\Goods;
use App\Models\HomeColumn;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $type = auth()->user()->type;

        $columnTypes = cons('home_column.type');
        //商品
        $goodsColumns = HomeColumn::where('type', $columnTypes['goods'])->get();

        $goodsFields = [
            'id',
            'name',
            'price_retailer',
            'price_wholesaler',
            'is_new',
            'is_out',
            'is_promotion',
            'sales_volume'
        ];
        foreach ($goodsColumns as $goodsColumn) {
            $goods = Goods::whereIn('id', $goodsColumn->id_list)->where('user_type', '>',
                $type)->select($goodsFields)->get();
            $columnGoodsCount = $goods->count();
            if ($columnGoodsCount < 10) {
                $columnGoodsIds = $goods->pluck('id')->toArray();
                $goodsBySort = Goods::whereNotIn('id', $columnGoodsIds)
                    ->where('user_type', '>', $type)
                    ->{'Of' . ucfirst(camel_case($goodsColumn->sort))}()
                    ->select($goodsFields)
                    ->take(10 - $columnGoodsCount)
                    ->get();
                $goods = $goods->merge($goodsBySort);
            }
            $goodsColumn->goods = $goods;
        }

        //商家
        $shopColumns = HomeColumn::where('type', $columnTypes['shop'])->get();

        foreach ($shopColumns as $shopColumn) {
            $shops = Shop::whereIn('id', $shopColumn->id_list)->whereHas('user', function ($q) use ($type) {
                $q->where('type', '>', $type);
            }
            )->get();
            $columnShopsCount = $shops->count();
            if ($columnShopsCount < 10) {
                $columnShopsIds = $shops->pluck('id')->toArray();
                $ShopsBySort = Shop::whereNotIn('id', $columnShopsIds)
                    ->whereHas('user', function ($q) use ($type) {
                        $q->where('type', '>', $type);
                    })
                    ->{'Of' . ucfirst(camel_case($shopColumn->sort))}()
                    ->take(10 - $columnShopsCount)
                    ->get();
                $shops = $shops->merge($ShopsBySort);
            }
            $shopColumn->shops = $shops;
        }
        $nowTime = Carbon::now();
        //广告
        $adverts = Advert::with('image')->where('type', cons('advert.type.index'))->OfTime($nowTime)->get();
        return view('index.index.index', [
            'goodsColumns' => $goodsColumns,
            'shopColumns' => $shopColumns,
            'adverts' => $adverts
        ]);
    }
}
