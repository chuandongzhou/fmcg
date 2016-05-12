<?php

namespace App\Http\Controllers\Index;

use App\Models\Advert;
use App\Models\Notice;
use App\Services\GoodsService;
use Carbon\Carbon;
use DB;
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
        $nowTime = Carbon::now();
        //广告
        $indexAdvertConf = cons('advert.cache.index');
        $adverts = [];
        if (Cache::has($indexAdvertConf['name'])) {
            $adverts = Cache::get($indexAdvertConf['name']);
        } else {
            $adverts = Advert::with('image')->where('type',
                cons('advert.type.index'))->OfTime($nowTime)->get()->each(function ($advert) {
                $advert->setAppends(['image_url'])->addHidden(['image', 'type', 'start_at', 'end_at']);
            });
            Cache::put($indexAdvertConf['name'], $adverts, $indexAdvertConf['expire']);
        }
        return view('index.index.index', [
            'goodsColumns' => GoodsService::getNewGoodsColumn(),
//            'shopColumns' => ShopService::getShopColumn(),
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

    public function test()
    {

    }
}
