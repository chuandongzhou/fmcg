<?php

namespace App\Http\Controllers\Index\Business;

use App\Models\Goods;
use App\Models\SalesmanCustomer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Index\Controller;
use Gate;

class SalesmanCustomerController extends Controller
{

    protected $shop;

    public function __construct()
    {
        $this->shop = auth()->user()->shop;
    }


    /**
     * 客户列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $salesmanId = $request->input('salesman_id');
        $name = $request->input('name');

        $salesmen = $this->shop->salesmen()->get(['id', 'name']);

        $customers = SalesmanCustomer::whereIn('salesman_id',
            $salesmen->pluck('id'))
            ->OfSalesman($salesmanId)
            ->OfName($name)
            ->with('salesman')
            ->get();

        return view('index.business.salesman-customer-index',
            ['salesmen' => $salesmen, 'customers' => $customers, 'salesmanId' => $salesmanId, 'name' => $name]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $salesmen = $this->shop->salesmen()->active()->lists('name', 'id');
        return view('index.business.salesman-customer',
            ['salesmen' => $salesmen, 'salesmanCustomer' => new SalesmanCustomer]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        //
    }

    public function show(Request $request, SalesmanCustomer $customer)
    {
        if ($customer && Gate::denies('validate-customer', $customer)) {
            return $this->error('客户不存在');
        }

        $data = $request->all();
        $beginTime = isset($data['begin_time']) ? new Carbon($data['begin_time']) : (new Carbon())->startOfMonth();
        $endTime = isset($data['end_time']) ? (new Carbon($data['end_time']))->endOfDay() : Carbon::now();

        //拜访记录
        $visits = $customer->visits()->OfTime($beginTime, $endTime)->with(['orders.orderGoods', 'goodsRecord'])->get();


        //拜访商品记录
        $goodsRecodeData = [];
        $goodsRecode = $visits->pluck('goodsRecord')->collapse();
        foreach ($goodsRecode as $record) {
            if (!is_null($record)) {
                $goodsRecodeData[$record->goods_id][$record->salesman_visit_id] = $record;
            }
        }


        //拜访时产生的订单和退货单
        $allOrders = $visits->pluck('orders')->collapse()->filter(function ($item) {
            return !is_null($item);
        });


        $orderConf = cons('salesman.order');

        //订单
        $orders = $allOrders->filter(function ($item) use ($orderConf) {
            return $item->type == $orderConf['type']['order'];
        });

        //退货单
        $returnOrders = $allOrders->filter(function ($item) use ($orderConf) {
            return $item->type == $orderConf['type']['return_order'];
        });

        // 所有的订单商品
        $orderGoods = $allOrders->pluck('orderGoods')->collapse();

        //所有订单商品详情
        $orderGoodsDetail = Goods::whereIn('id', $orderGoods->pluck('goods_id')->toBase()->unique())->lists('name',
            'id');

        //货抵
        $mortgageGoods = $orderGoods->filter(function ($item) use ($orderConf) {
            return $item->type == $orderConf['goods']['type']['mortgage'];
        });

        //客户销售的商品
        $salesList = [];
        foreach ($orderGoods as $goods) {
            $salesList[$goods->goods_id][$goods->salesman_visit_id][$goods->type] = $goods;
        }
        
        $salesListsData = [];

        $orderGoodsType = $orderConf['goods']['type'];

        foreach ($salesList as $goodsId => $goodsVisits) {
            $salesListsData[$goodsId]['id'] = $goodsId;
            $salesListsData[$goodsId]['name'] = $orderGoodsDetail[$goodsId];
            foreach ($goodsVisits as $visitId => $goodsList) {
                $salesListsData[$goodsId]['visit'][] = [
                    'time' => head($goodsList)->created_at,
                    'stock' => isset($goodsRecodeData[$goodsId]) ? $goodsRecodeData[$goodsId][$visitId]->stock : 0,
                    'production_date' => isset($goodsRecodeData[$goodsId]) ? $goodsRecodeData[$goodsId][$visitId]->production_date : 0,
                    'order_num' => isset($goodsList[$orderGoodsType['order']]) ? $goodsList[$orderGoodsType['order']]->num : 0,
                    'order_price' => isset($goodsList[$orderGoodsType['order']]) ? $goodsList[$orderGoodsType['order']]->price : 0,
                    'order_pieces' => isset($goodsList[$orderGoodsType['order']]) ? $goodsList[$orderGoodsType['order']]->pieces : 0,
                    'order_amount' => isset($goodsList[$orderGoodsType['order']]) ? $goodsList[$orderGoodsType['order']]->amount : 0,
                    'return_num' => isset($goodsList[$orderGoodsType['return']]) ? $goodsList[$orderGoodsType['return']]->num : 0,
                    'return_amount' => isset($goodsList[$orderGoodsType['return']]) ? $goodsList[$orderGoodsType['return']]->amount : 0,
                ];
            }
        }
        return view('index.business.salesman-customer-detail', [
            'beginTime' => $beginTime->toDateString(),
            'endTime' => $endTime->toDateString(),
            'visits' => $visits,
            'customer' => $customer,
            'orders' => $orders,
            'orderGoodsDetail' => $orderGoodsDetail,
            'returnOrders' => $returnOrders,
            'mortgageGoods' => $mortgageGoods,
            'salesListsData' => $salesListsData
        ]);
    }


    /**
     * 客户编辑
     *
     * @param $salesCustomer
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($salesCustomer)
    {
        $salesmen = $this->shop->salesmen()->active()->lists('name', 'id');
        return view('index.business.salesman-customer',
            ['salesmen' => $salesmen, 'salesmanCustomer' => $salesCustomer]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
