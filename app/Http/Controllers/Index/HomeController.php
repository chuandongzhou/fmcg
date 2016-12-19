<?php

namespace App\Http\Controllers\Index;

use App\Models\Advert;
use App\Models\Goods;
use App\Models\Model;
use App\Models\Order;
use App\Services\GoodsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Zend\Http\Client\Adapter\Curl;

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
                cons('advert.type.index'))->OfTime()->get();
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

    public function test()
    {

    }

}
