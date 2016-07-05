<?php

namespace App\Http\Controllers\Api\V1\Business;

use App\Models\SalesmanVisit;
use App\Models\SalesmanVisitGoodsRecord;
use App\Models\SalesmanVisitOrderGoods;
use App\Services\BusinessService;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Api\V1\Controller;
use DB;

class SalesmanVisitController extends Controller
{
    public function __construct()
    {
        $this->middleware('salesman.auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $salesman = salesman_auth()->user();
        $visit = $salesman->visits()->with('salesmanCustomer')->get();
        return $this->success(compact('visit'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*$data = [
            'salesman_customer_id' => 1,
            'goods' => [
                [
                    'id' => 324,
                    'pieces' => 11,
                    'stock'  => '3件',
                    'production_date' => '2015-06-09',
                    'order_form' => [
                        'price'  => '169',
                        'num'    => 2
                    ],
                    'return_order' => [
                        'amount'  => 160,
                        'num'     => 2
                    ]
                ]
            ],
            'display_fee' => '100',
            "mortgage" => [
                [
                    "goods_id"  => 324,
                    "num"       => 1,
                    "pieces"    => 11,
                ],

            ]

        ];
       dd($data);*/
        $data = $request->all();
        $salesman = salesman_auth()->user();

        $result = DB::transaction(function () use ($salesman, $data) {
            $result = $this->_formatData($data);
            $visit = $salesman->visits()->create(['salesman_customer_id' => $data['salesman_customer_id']]);

            $orderConf = cons('salesman.order');

            if ($visit->exists) {
                if ($orderForms = array_filter($result['order']['order_form'])) {
                    $orderForms['salesman_visit_id'] = $visit->id;
                    $orderForms['salesman_customer_id'] = $data['salesman_customer_id'];
                    $orderForms['type'] = $orderConf['type']['order'];
                    $orderForm = $salesman->orders()->create($orderForms);
                    if ($orderForm->exists) {
                        $orderGoodsArr = [];
                        $mortgageGoodsArr = [];
                        foreach ($orderForms['goods'] as $orderGoods) {
                            $orderGoods['salesman_visit_id'] = $visit->id;
                            $orderGoods['type'] = $orderConf['goods']['type']['order'];
                            $orderGoodsArr[] = new SalesmanVisitOrderGoods($orderGoods);
                        }
                        $orderForm->orderGoods()->saveMany($orderGoodsArr);

                        if ($data['mortgage']) {
                            foreach ($data['mortgage'] as $mortgageGoods) {
                                $mortgageGoods['salesman_visit_id'] = $visit->id;
                                $mortgageGoods['type'] = $orderConf['goods']['type']['mortgage'];
                                $mortgageGoodsArr[] = new SalesmanVisitOrderGoods($mortgageGoods);
                            }
                        }
                        $orderForm->orderGoods()->saveMany($mortgageGoodsArr);
                    }
                }
                if (isset($result['order']['return_order'])) {
                    $result['order']['return_order']['salesman_visit_id'] = $visit->id;
                    $result['order']['return_order']['type'] = $orderConf['type']['return_order'];
                    $returnOrder = $salesman->orders()->create($result['order']['return_order']);
                    if ($returnOrder->exists) {
                        $orderGoodsArr = [];
                        foreach ($result['order']['return_order']['goods'] as $orderGoods) {
                            $orderGoods['salesman_visit_id'] = $visit->id;
                            $orderGoods['type'] = $orderConf['goods']['type']['return'];
                            $orderGoodsArr[] = new SalesmanVisitOrderGoods($orderGoods);
                        }
                        info($result['order']['return_order']['goods']);
                        info($orderGoodsArr);

                        $returnOrder->orderGoods()->saveMany($orderGoodsArr);


                    }
                }
                if ($goodsRecodes = $result['goodsRecode']) {
                    $goodsRecodeArr = [];
                    foreach ($goodsRecodes as $goodsRecode) {
                        $goodsRecodeArr[] = new SalesmanVisitGoodsRecord($goodsRecode);
                    }
                    $visit->goodsRecord()->saveMany($goodsRecodeArr);
                }
            }
            return 'success';
        });
        return $result === 'success' ? $this->success('拜访记录添加成功') : $this->error('拜访记录添加时出现错误');
    }

    /**
     * 拜访详情
     *
     * @param \App\Models\SalesmanVisit $visit
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function show(SalesmanVisit $visit)
    {
        if (!$visit || $visit->salesman_id != salesman_auth()->id()) {
            return $this->error('拜访信息出错');
        }
        $visit->load([
            'orders.orderGoods.mortgageGoods',
            'goodsRecord',
            'salesmanCustomer.shippingAddress',
            'orders.orderGoods.goods'
        ]);
        $visitData = head((new BusinessService())->formatVisit([$visit], true));
        $visitData['display_fee'] = head($visitData['display_fee']) ? head($visitData['display_fee'])['display_fee'] : 0;
        $visitData['mortgage'] = head($visitData['mortgage']) ? head($visitData['mortgage']) : [];
        $visitData['statistics'] = array_values($visitData['statistics']);
        return $this->success(compact('visitData'));
    }

    /**
     * 格式化拜访记录
     *
     * @param $data
     * @return array
     */
    private function _formatData($data)
    {
        $order = [];
        $goodsRecode = [];

        foreach ($data['goods'] as $goods) {
            if (isset($goods['order_form'])) {
                $order['order_form']['display_fee'] = isset($data['display_fee']) ? $data['display_fee'] : 0;
                $order['order_form']['amount'] = isset($order['order_form']['amount']) ?
                    bcadd($order['order_form']['amount'],
                        bcmul($goods['order_form']['price'], $goods['order_form']['num'], 2), 2) :
                    bcmul($goods['order_form']['price'], $goods['order_form']['num'], 2);
                $order['order_form']['goods'][] = [
                    'goods_id' => $goods['id'],
                    'price' => $goods['order_form']['price'],
                    'num' => $goods['order_form']['num'],
                    'pieces' => $goods['pieces'],
                    'amount' => bcmul($goods['order_form']['price'], $goods['order_form']['num'], 2)
                ];
            }

            if (isset($goods['return_order'])) {
                $order['return_order']['amount'] = isset($order['return_order']['amount']) ?
                    bcadd($order['return_order']['amount'], $goods['return_order']['amount'],
                        2) : $goods['return_order']['amount'];
                $order['return_order']['goods'][] = [
                    'goods_id' => $goods['id'],
                    'num' => $goods['return_order']['num'],
                    'amount' => $goods['return_order']['amount']
                ];
            }
            $goodsRecode[] = [
                'goods_id' => $goods['id'],
                'stock' => $goods['stock'],
                'production_date' => $goods['production_date']
            ];

        }
        return compact('order', 'goodsRecode');
    }
}
