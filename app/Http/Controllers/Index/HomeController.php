<?php

namespace App\Http\Controllers\Index;

use App\Models\Advert;
use App\Services\AddressService;
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

    public function test()
    {
      dd(app('wechat.pay')->verifySign(array (
          'bankNumber' => 'NjIxNTU4NDQwMjAxMDgxMjMzNw==',
          'bankCode' => 'ICBC',
          'orderNo' => '12',
          'dealMsg' => '交易成功',
          'accountName' => '5p6X5pmT5Lic',
          'fee' => '150.0',
          'sign' => '8587394AF09AF16A044F0F465AC2F91B',
          'bankName' => '工商银行',
          'cxOrderNo' => '2017040100011796fe97759aa27a0c9',
          'orderAmount' => '1000.0',
          'orderTime' => '20170401065853',
          'dealTime' => '20170401070003',
          'dealCode' => '10000',
          'currency' => 'CNY',
          'merchantNo' => 'CX0001089',
      )));
    }

}
