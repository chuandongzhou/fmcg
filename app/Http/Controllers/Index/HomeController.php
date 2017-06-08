<?php

namespace App\Http\Controllers\Index;

use App\Models\Advert;
use App\Models\Order;
use App\Models\Shop;
use App\Services\GoodsService;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        //广告
        $indexAdvertConf = cons('advert.cache.index');
        $adverts = [];
        if (Cache::has($indexAdvertConf['name'])) {
            $adverts = Cache::get($indexAdvertConf['name']);
        } else {
            $adverts = Advert::with('image')->where('type',
                cons('advert.type.index'))->OfTime()->orderBy('sort', 'DESC')->get();
            Cache::put($indexAdvertConf['name'], $adverts, $indexAdvertConf['expire']);
        }
        return view('index.index.index', [
            'goodsColumns' => GoodsService::getNewGoodsColumn(),
            'adverts' => $adverts,
        ]);
    }

    /**
     * 关于我们
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function about()
    {
        return view('index.index.about');
    }

    public function test(Request $request)
    {
        $shopId = $request->input('shop_id');
        $shop = Shop::find($shopId);
        if (is_null($shop)) {
            dd('店铺不存在');
        }
        $shopType = $shop->user_type;

        $goods = $shop->goods()->with('goodsPieces')->paginate(100);

        foreach ($goods as $item) {
            $tag = false;
            if ($item->specification_retailer != ($specificationRetailer = GoodsService::getPiecesSystem2($item, $item->pieces_retailer))){
                $item->specification_retailer = $specificationRetailer;
                $tag = true;
            }

            if ($shopType == 3) {
                if ($item->specification_wholesaler != ($specificationWholesaler = GoodsService::getPiecesSystem2($item, $item->pieces_wholesaler))){
                    $item->specification_wholesaler = $specificationWholesaler;
                    $tag = true;
                }
            }
            if ($tag) {
                $item->save();
            }
        }
        return view('index.index.test', compact('goods'));
    }

}
