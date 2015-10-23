<?php

namespace App\Http\Controllers\Index;

use App\Models\Advert;
use App\Models\Goods;
use App\Models\HomeColumn;
use App\Models\Shop;
use App\Services\GoodsService;
use App\Services\ShopService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;

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
        $adverts = Advert::with('image')->where('type', cons('advert.type.index'))->OfTime($nowTime)->get();
        return view('index.index.index', [
            'goodsColumns' => GoodsService::getGoodsColumn(),
            'shopColumns' => ShopService::getShopColumn(),
            'adverts' => $adverts
        ]);
    }
}
