<?php

namespace App\Http\Controllers\Index;

use App\Models\Advert;
use App\Models\OrderGoods;
use App\Services\GoodsService;
use Carbon\Carbon;
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
        /* $orderGoods = OrderGoods::with('order')->where('created_at', '0000-00-00 00:00:00')->get();

         foreach ($orderGoods as $orderGood) {
             $order = $orderGood->order;
             $createdAt = $order ? $order->created_at : new Carbon();
             $orderGood->fill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();
         }*/
    }

}
