<?php

namespace App\Http\Controllers\Index;

use App\Models\Advert;
use App\Models\Order;
use App\Services\GoodsService;
use App\Services\ShopService;
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
        if (auth()->user()->type == cons('user.type.supplier')) {
            return redirect('personal/info');
        }

        $nowTime = Carbon::now();
        //广告
        $indexAdvertConf = cons('advert.cache.index');
        $adverts = [];
        if (Cache::has($indexAdvertConf['name'])) {
            $adverts = Cache::get($indexAdvertConf['name']);
        } else {
            $adverts = Advert::with('image')->where('type', cons('advert.type.index'))->OfTime($nowTime)->get();
            Cache::put($indexAdvertConf['name'], $adverts, $indexAdvertConf['expire']);
        }

        return view('index.index.index', [
            'goodsColumns' => GoodsService::getGoodsColumn(),
            'shopColumns' => ShopService::getShopColumn(),
            'adverts' => $adverts
        ]);
    }

    public function test()
    {

        /*$orders = Order::where([
            'pay_type' => cons('pay_type.online'),
            'status' => cons('order.status.send'),
            'pay_status' => cons('order.pay_status.payment_success'),
        ])->where('send_at', '<=', Carbon::now()->subDays(3))->nonCancel()->get();

        dd($orders);*/
    }
}
