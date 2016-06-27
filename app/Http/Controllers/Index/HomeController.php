<?php

namespace App\Http\Controllers\Index;

use App\Models\Advert;
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
        $data = [
            'goods' => [
                [
                    'id',
                    'pieces',
                    'stock',
                    'production_date',
                    'order_form' => [
                        'price',
                        'num'
                    ],
                    'return_order' => [
                        'amount',
                        'num'
                    ]
                ]
            ],
            'display_fee',
            "mortgage" => [
                [
                    "goods_id",
                    "num",
                    "pieces",
                ],

            ]

        ];

        $order = [];
        $goodsRecode = [];
        $order['order_form']['display_fee'] = isset($data['display_fee']) ? $data['display_fee'] : 0;

        foreach ($data['goods'] as $goods) {
            if ($goods['order_form']) {
                $order['order_form']['amount'] = isset($order['order_form']['amount']) ?
                    bcadd($order['order_form']['amount'],
                        bcmul($goods['order_form']['price'], $goods['order_form']['num'], 2), 2) :
                    bcmul($goods['order_form']['amount'], $goods['order_form']['num'], 2);
                $order['order_form']['goods'][] = [
                    'goods_id' => $goods['id'],
                    'price' => $goods['order_form']['price'],
                    'num' => $goods['order_form']['num'],
                    'pieces' => $goods['pieces'],
                    'amount' => bcmul($goods['order_form']['price'], $goods['order_form']['num'], 2)
                ];
            }

            if ($goods['return_order']) {
                $order['return_order']['amount'] = isset($order['return_order']['amount']) ?
                    bcadd($order['return_order']['amount'], $goods['return_order']['amount'],
                        2) : $goods['return_order']['amount'];
                $order['return_order']['goods'][] = [
                    'goods_id' => $goods['id'],
                    'num' => $goods['order_form']['num'],
                    'amount' => bcmul($goods['order_form']['price'], $goods['order_form']['num'], 2)
                ];
            }
            $goodsRecode[] = [
                'goods_id' => $goods['id'],
                'stock' => $goods['stock'],
                'production_date' => $goods['production_date']
            ];

        }

    }
}
