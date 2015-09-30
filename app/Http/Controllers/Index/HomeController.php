<?php

namespace App\Http\Controllers\Index;

use App\Models\Advert;
use App\Models\Goods;
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
        //热门商品
        $hotGoods = Goods::hot()->where('user_type', '>', $type)->take(16)->get();
        //热门商家
        $hotShops = Shop::ofHot()->whereHas('user', function ($q) use ($type) {
            $q->where('type', '>', $type);
        }
        )->get();
        $nowTime = Carbon::now();
        //广告
        $adverts = Advert::with('image')->where('type', cons('advert.type.index'))->OfTime($nowTime)->get();
        return view('index.index.index', ['hotGoods' => $hotGoods, 'hotShops' => $hotShops, 'adverts' => $adverts]);
    }
}
