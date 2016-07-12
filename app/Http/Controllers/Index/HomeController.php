<?php

namespace App\Http\Controllers\Index;

use App\Models\Advert;
use App\Models\Salesman;
use App\Models\SalesmanCustomer;
use App\Services\GoodsService;

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
        array(
            'display_fee' => '9',
            'goods' =>
                array(
                    0 =>
                        array(
                            'id' => '374',
                        ),
                    1 =>
                        array(
                            'order_form' =>
                                array(
                                    'num' => '2',
                                ),
                        ),
                    2 =>
                        array(
                            'order_form' =>
                                array(
                                    'price' => '12',
                                ),
                        ),
                    3 =>
                        array(
                            'pieces' => '0',
                        ),
                    4 =>
                        array(
                            'production_date' => '2016-07-13',
                        ),
                    5 =>
                        array(
                            'return_order' =>
                                array(
                                    'amount' => '13.5',
                                ),
                        ),
                    6 =>
                        array(
                            'return_order' =>
                                array(
                                    'num' => '9',
                                ),
                        ),
                    7 =>
                        array(
                            'stock' => '3',
                        ),
                ),
            'mortgage' =>
                array(
                    0 =>
                        array(
                            'goods_id' => '377',
                        ),
                    1 =>
                        array(
                            'num' => '6',
                        ),
                ),
            'salesman_customer_id' => '8',
        )  ;

    }
}
