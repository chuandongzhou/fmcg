<?php

namespace App\Http\Controllers\Mobile;

use App\Models\Advert;
use App\Services\GoodsService;
use Cache;

class HomeController extends Controller
{
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
        return view('mobile.index.index', [
            'goodsColumns' => GoodsService::getNewGoodsColumn(),
            'adverts' => $adverts,
        ]);
    }

}
