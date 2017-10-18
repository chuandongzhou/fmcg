<?php

namespace App\Http\Controllers\Index\Business;

use App\Models\Goods;
use App\Models\Order;
use App\Models\SalesmanCustomer;
use App\Models\SalesmanVisitOrder;
use App\Services\BillService;
use App\Services\BusinessService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Index\Controller;
use Gate;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use PhpOffice\PhpWord\PhpWord;

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
        $type = $request->input('type', null);
        $storeType = $request->input('store_type', null);
        $areaId = $request->input('area_id', null);
        $salesmen = $this->shop->salesmen()->get(['id', 'name']);
        $areas = $this->shop->areas()->get(['id', 'name']);
        $user = auth()->user();
        $customers = SalesmanCustomer::OfSalesman($salesmanId)
            ->OfName($name)
            ->where(function ($query) use ($type, $storeType, $areaId, $salesmen, $user) {
                if ($user->type == cons('user.type.supplier')) {
                    $query->where(function ($query) use ($salesmen) {
                        $query->where('belong_shop', $this->shop->id)
                            ->orWhereIn('salesman_id', $salesmen->pluck('id'));
                    });
                } else {
                    $query->whereIn('salesman_id', $salesmen->pluck('id'));
                }
                if ($type == 'supplier') {
                    $query->where('type', cons('user.type.supplier'));
                }
                if (is_null($type)) {
                    $query->where('type', '<>', cons('user.type.supplier'));
                }
                if (!is_null($storeType)) {
                    $query->where('store_type', $storeType);
                }
                if (!is_null($areaId)) {
                    $query->where('area_id', $areaId);
                }
            })
            ->with('salesman', 'businessAddress', 'shippingAddress', 'shop.user')
            ->paginate();
        return view('index.business.salesman-customer-index',
            [
                'salesmen' => $salesmen,
                'customers' => $customers,
                'type' => $type,
                'data' => $request->all(),
                'areas' => $areas,
                'relationApply' => $this->shop->businessRelation()->notActive()->count(),
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $salesmen = $this->shop->salesmen()->lists('name', 'id');
        $areas = $this->shop->areas()->get(['id', 'name']);
        return view('index.business.salesman-customer',
            [
                'salesmen' => $salesmen,
                'salesmanCustomer' => new SalesmanCustomer,
                'customerType' => $request->input('type', null),
                'areas' => $areas
            ]);
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

    /**
     * 客户信息销售明细
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SalesmanCustomer $customer
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function show(Request $request, SalesmanCustomer $customer)
    {
        if ($customer && Gate::denies('validate-customer', $customer)) {
            return $this->error('客户不存在');
        }

        $data = $request->all();
        $beginTime = isset($data['begin_time']) ? new Carbon($data['begin_time']) : (new Carbon())->startOfMonth();
        $endTime = isset($data['end_time']) ? (new Carbon($data['end_time']))->endOfDay() : Carbon::now();

        $result = $this->_getCustomerDetail($customer, $beginTime, $endTime);

        $result = array_merge($result, [
            'type' => $request->input('type', null),
            'beginTime' => $beginTime->toDateString(),
            'endTime' => $endTime->toDateString(),
            'customer' => $customer,
        ]);

        return view('index.business.salesman-customer-detail', $result);
    }

    /**
     * 客户信息销售明细导出
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SalesmanCustomer $customer
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function export(Request $request, SalesmanCustomer $customer)
    {
        if ($customer && Gate::denies('validate-customer', $customer)) {
            return $this->error('客户不存在');
        }
        $data = $request->all();
        $beginTime = isset($data['begin_time']) ? new Carbon($data['begin_time']) : (new Carbon())->startOfMonth();
        $endTime = isset($data['end_time']) ? (new Carbon($data['end_time']))->endOfDay() : Carbon::now();

        $result = $this->_getCustomerDetail($customer, $beginTime, $endTime);

        $result = array_merge($result, [
            'beginTime' => $beginTime->toDateString(),
            'endTime' => $endTime->toDateString(),
            'customer' => $customer,
        ]);

        return $this->_export($result);
    }

    /**
     * 客户编辑
     *
     * @param \Illuminate\Http\Request $request
     * @param $salesmanCustomer
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $salesmanCustomer)
    {
        $salesmen = $this->shop->salesmen()->active()->lists('name', 'id');
        $areas = $this->shop->areas()->get(['id', 'name']);
        return view('index.business.salesman-customer',
            [
                'salesmen' => $salesmen,
                'salesmanCustomer' => $salesmanCustomer,
                'customerType' => $request->input('type', null),
                'areas' => $areas
            ]);
    }

    /**
     * 客户对账单
     *
     * @param $salesmanCustomer
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bill(Request $request, $customer)
    {
        if ($customer && Gate::denies('validate-customer', $customer)) {
            return $this->error('客户不存在');
        }
        $billService = new BillService();
        $data = $request->all();
        $timeInterval = $billService->timeHandler(array_get($data, 'time'));

        $result = $billService->seller($customer, $timeInterval);

        $action = array_get($data, 'act');

        if ($action === 'export') {
            return $billService->export($result, $customer, $timeInterval);
        }

        return view($action === 'print' ? 'index.business.bill-print-template' : 'index.business.customer-bill',
            [
                'data' => $data,
                'timeInterval' => $timeInterval,
                'customer' => $customer,
                'bill' => $result,
                'action' => $action === 'print' ? 'print' : null
            ]);
    }

    public function getStockQuery(Request $request, $customer)
    {
        $data = $request->only('name_code');
        //店家商品库 shop goods library
        $sgl_codes = auth()->user()->shop->goods->pluck('bar_code');
        //客户商品库 customer goods library
        $cgl = $customer->shop->goods()->ofNameOrCode($data['name_code'])->whereIn('bar_code',
            $sgl_codes);
        if ($request->input('action') === 'exp' && count($cgl->get())) {
            return $this->stockQueryExport($cgl->get(), Carbon::now()->toDateString() . $customer->name . '商品库存查询');
        }
        return view('index.business.stock-query-list', [
            'customer' => $customer,
            'cgl' => $cgl->paginate(),
            'data' => $data
        ]);
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

    /**
     * 获取客户销售明细详情
     *
     * @param \App\Models\SalesmanCustomer $customer
     * @param $beginTime
     * @param $endTime
     * @return array
     */
    private function _getCustomerDetail(SalesmanCustomer $customer, $beginTime, $endTime)
    {

        //拜访记录
        $visits = $customer->visits()->OfTime($beginTime, $endTime)->with([
            'goodsRecord'
        ])->get();

        //拜访商品记录
        $goodsRecodeData = [];
        $goodsRecord = $visits->pluck('goodsRecord')->collapse();
        foreach ($goodsRecord as $record) {
            if (!is_null($record)) {
                $goodsRecodeData[$record->goods_id][$record->salesman_visit_id] = $record;
            }
        }


        //拜访时产生的订单和退货单
        /* $allOrders = $visits->pluck('orders')->collapse()->filter(function ($item) {
             return !is_null($item);
         });*/

        $allOrders = SalesmanVisitOrder::active()->where('salesman_customer_id', $customer->id)->ofData([
            'start_date' => $beginTime,
            'end_date' => $endTime
        ])->with('orderGoods', 'mortgageGoods', 'order.coupon')->get()->filter(function ($item) {
            $order = $item->order;
            if (!is_null($order)) {
                return $order->is_cancel == 0
                && $order->pay_status < cons('order.pay_status.payment_failed')
                && $order->status != cons('order.status.invalid');
            }
            return true;
        });


        $orderConf = cons('salesman.order');

        $allOrders->each(function ($order) {
            $order->orderGoods->each(function ($goods) use ($order) {
                $goods->visit_created_at = $order->salesmanVisit ? $order->salesmanVisit->created_at : $goods->created_at;

            });
            $order->gifts->each(function ($gifts) use ($order) {
                $gifts->order_id = $order->order_id;
                $gifts->order_created_at = $order->created_at;
            });
        });

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

        //所有赠品
        $gifts = $orders->pluck('gifts')->collapse();

        $goodsIds = array_merge(array_keys($goodsRecodeData), $orderGoods->pluck('goods_id')->all());


        //所有订单商品详情
        $orderGoodsDetail = Goods::whereIn('id', array_unique($goodsIds))->withTrashed()->lists('name', 'id');

        $businessService = new BusinessService();

        //货抵
        $mortgageGoods = $businessService->getOrderMortgageGoods($orders)->groupBy('created_at');


        //陈列费
        $displayFees = $businessService->getOrderDisplayFees($orders);


        //客户销售的商品
        $salesList = [];


        foreach ($orderGoods as $goods) {
            $salesList[$goods->goods_id][$goods->salesman_visit_id][$goods->type] = $goods;
        }

        foreach ($goodsRecord as $key => $record) {
            $tag = false;
            foreach ($orderGoods as $goods) {
                if ($record->goods_id == $goods->goods_id && $record->salesman_visit_id == $goods->salesman_visit_id) {
                    $tag = true;
                    break;
                }
            }
            if (!$tag) {
                $salesList[$record->goods_id][$record->salesman_visit_id][-1] = ['created_at' => $record->visit->created_at];
            }
        }


        $salesListsData = [];

        $orderGoodsType = $orderConf['goods']['type'];


        foreach ($salesList as $goodsId => $goodsVisits) {
            $salesListsData[$goodsId]['id'] = $goodsId;
            $salesListsData[$goodsId]['name'] = $orderGoodsDetail[$goodsId];

            foreach ($goodsVisits as $visitId => $goodsList) {
                $salesListsData[$goodsId]['visit'][$visitId] = [
                    'time' => isset($goodsList[$orderGoodsType['order']]) ? $goodsList[$orderGoodsType['order']]->visit_created_at : head($goodsList)['created_at'],
                    'stock' => isset($goodsRecodeData[$goodsId]) && isset($goodsRecodeData[$goodsId][$visitId]) ? $goodsRecodeData[$goodsId][$visitId]->stock : 0,
                    'production_date' => isset($goodsRecodeData[$goodsId]) && isset($goodsRecodeData[$goodsId][$visitId]) ? $goodsRecodeData[$goodsId][$visitId]->production_date : 0,
                    'order_num' => isset($goodsList[$orderGoodsType['order']]) ? $goodsList[$orderGoodsType['order']]->num : 0,
                    'order_price' => isset($goodsList[$orderGoodsType['order']]) ? $goodsList[$orderGoodsType['order']]->price : 0,
                    'order_pieces' => isset($goodsList[$orderGoodsType['order']]) ? $goodsList[$orderGoodsType['order']]->pieces : 0,
                    'order_amount' => isset($goodsList[$orderGoodsType['order']]) ? $goodsList[$orderGoodsType['order']]->amount : 0,
                    'return_num' => isset($goodsList[$orderGoodsType['return']]) ? $goodsList[$orderGoodsType['return']]->num : 0,
                    'return_amount' => isset($goodsList[$orderGoodsType['return']]) ? $goodsList[$orderGoodsType['return']]->amount : 0,
                ];
            }
        }
        $giftList = $giftsExist = [];
        foreach ($gifts as $gift) {
            if (!in_array($gift->id, $giftsExist)) {
                $giftList[$gift->id] = [
                    'name' => $gift->name,
                    'describe' => [
                        [
                            'order_id' => $gift->order_id,
                            'time' => $gift->order_created_at,
                            'num' => $gift->pivot->num,
                            'pieces' => $gift->pivot->pieces,
                        ]
                    ]
                ];
            } else {
                $giftList[$gift->id]['describe'][] = [
                    'order_id' => $gift->order_id,
                    'time' => $gift->order_created_at,
                    'num' => $gift->pivot->num,
                    'pieces' => $gift->pivot->pieces,
                ];
            }
            $giftsExist[] = $gift->id;
        }
        return [
            'visits' => $visits,
            'orders' => $orders,
            //'orderGoodsDetail' => $orderGoodsDetail,
            'returnOrders' => $returnOrders,
            'mortgageGoods' => $mortgageGoods,
            'displayFees' => $displayFees,
            'salesListsData' => $salesListsData,
            'gifts' => collect($giftList)
        ];
    }


    /**
     * 导出
     *
     * @param $result
     */
    private function _export($result)
    {
        // Creating the new document...
        $phpWord = new PhpWord();

        $tableBolder = array('borderSize' => 1, 'borderColor' => '999999');


        $cellAlignCenter = ['align' => 'center'];
        $cellVAlignCenter = ['valign' => 'center'];
        $gridSpan2 = ['gridSpan' => 2, 'valign' => 'center'];
        $gridSpan5 = ['gridSpan' => 5, 'valign' => 'center'];
        $gridSpan4 = ['gridSpan' => 4, 'valign' => 'center'];
        $gridSpan10 = ['gridSpan' => 10, 'valign' => 'center'];
        $cellRowSpan = ['vMerge' => 'restart', 'valign' => 'center'];
        $cellRowContinue = array('vMerge' => 'continue');

        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(10);

        $phpWord->addParagraphStyle('Normal', [
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'lineHeight' => 1.2,  // 行间距
        ]);

        $section = $phpWord->addSection();
        $table = $section->addTable();
        $table->addRow();
        $table->addCell(2500)->addText('店铺名称 : ' . $result['customer']->name);
        $table->addCell(2500)->addText('联系人 : ' . $result['customer']->contact);
        $table->addCell(5500)->addText('联系电话 : ' . $result['customer']->contact_information);

        $table->addRow();
        $table->addCell(2500)->addText('业务员 : ' . $result['customer']->salesman->name);
        $table->addCell(2500)->addText('拜访次数 : ' . $result['visits']->count());
        $table->addCell(5500)->addText('最后拜访时间 : ' . $result['visits']->max('created_at'));

        $table->addRow();
        $table->addCell(2500)->addText('订货总订单数 : ' . $result['orders']->count());
        $table->addCell(8000, $gridSpan2)->addText('订单总金额  : ' . $result['orders']->sum('amount'));

        $table->addRow();
        $table->addCell(2500)->addText('退货总订单数 : ' . $result['returnOrders']->count());
        $table->addCell(8000, $gridSpan2)->addText('退货总金额  : ' . $result['returnOrders']->sum('amount'));


        $section = $phpWord->addSection();
        $table = $section->addTable($tableBolder);

        $table->addRow(0);
        $table->addCell(1500, $gridSpan5)->addText('陈列费明细', null, $cellAlignCenter);
        $table->addCell(3000)->addText('', null, $cellAlignCenter);
        $table->addCell(2000)->addText('', null, $cellAlignCenter);
        $table->addCell(2000)->addText('', null, $cellAlignCenter);
        $table->addCell(2000)->addText('', null, $cellAlignCenter);

        $table->addRow();
        $table->addCell(1500, $cellRowSpan)->addText('陈列费', null, $cellAlignCenter);

        if (isset($result['orders'])) {
            $table->addRow();
            $table->addCell(null, $cellRowContinue);
            $table->addCell(9000, $gridSpan4)->addText('现金', null, $cellAlignCenter);

            $table->addRow();
            $table->addCell(null, $cellRowContinue);
            $table->addCell(2000)->addText('月份', null, $cellAlignCenter);
            $table->addCell(3000)->addText('拜访时间', null, $cellAlignCenter);
            $table->addCell(4000, $gridSpan2)->addText('金额', null, $cellAlignCenter);

            foreach ($result['displayFees'] as $displayFee) {
                $table->addRow();
                $table->addCell(null, $cellRowContinue);
                $table->addCell(2000)->addText($displayFee['month'], null, $cellAlignCenter);
                $table->addCell(3000)->addText($displayFee['time'], null, $cellAlignCenter);
                $table->addCell(7300, $gridSpan2)->addText($displayFee['used'], null, $cellAlignCenter);
            }
        }

        if ($result['mortgageGoods']->count()) {
            $table->addRow();
            $table->addCell(null, $cellRowContinue);
            $table->addCell(9000, $gridSpan4)->addText('货抵 ', null, $cellAlignCenter);

            $table->addRow();
            $table->addCell(null, $cellRowContinue);
            $table->addCell(3000, $cellVAlignCenter)->addText('拜访时间', null, $cellAlignCenter);
            $table->addCell(2000, $cellVAlignCenter)->addText('商品名称', null, $cellAlignCenter);
            $table->addCell(2000, $cellVAlignCenter)->addText('商品单位', null, $cellAlignCenter);
            $table->addCell(2000, $cellVAlignCenter)->addText('数量', null, $cellAlignCenter);


            foreach ($result['mortgageGoods'] as $visitTime => $mortgages) {
                $table->addRow();
                $table->addCell(null, $cellRowContinue);
                $table->addCell(3000, $cellRowSpan)->addText($visitTime, null, $cellAlignCenter);
                foreach ($mortgages as $mortgage) {
                    $table->addRow();
                    $table->addCell(null, $cellRowContinue);
                    $table->addCell(3000, $cellRowContinue);
                    $table->addCell(2000, $cellVAlignCenter)->addText($mortgage['name'], null,
                        $cellAlignCenter);
                    $table->addCell(2000, $cellVAlignCenter)->addText(cons()->valueLang('goods.pieces',
                        $mortgage['pieces']),
                        null, $cellAlignCenter);
                    $table->addCell(2000, $cellVAlignCenter)->addText($mortgage['num'], null, $cellAlignCenter);
                }
            }
        }

        $section = $phpWord->addSection();
        $table = $section->addTable($tableBolder);

        $table->addRow();
        $table->addCell(10500, $gridSpan10)->addText('客户销售商品列表', null, $cellAlignCenter);

        $table->addRow();
        $table->addCell(800, $cellVAlignCenter)->addText('商品ID', null, $cellAlignCenter);
        $table->addCell(1700, $cellVAlignCenter)->addText('商品名称', null, $cellAlignCenter);
        $table->addCell(1000, $cellVAlignCenter)->addText('时间', null, $cellAlignCenter);
        $table->addCell(1000, $cellVAlignCenter)->addText('商品库存', null, $cellAlignCenter);
        $table->addCell(1000, $cellVAlignCenter)->addText('生产日期', null, $cellAlignCenter);
        $table->addCell(1000, $cellVAlignCenter)->addText('商品单价', null, $cellAlignCenter);
        $table->addCell(1000, $cellVAlignCenter)->addText('订货数量', null, $cellAlignCenter);
        $table->addCell(1000, $cellVAlignCenter)->addText('订货总金额', null, $cellAlignCenter);
        $table->addCell(1000, $cellVAlignCenter)->addText('退货数量', null, $cellAlignCenter);
        $table->addCell(1000, $cellVAlignCenter)->addText('退货总金额', null, $cellAlignCenter);

        foreach ($result['salesListsData'] as $goodsId => $salesGoods) {
            $table->addRow();
            $table->addCell(800, $cellRowSpan)->addText($salesGoods['id'], null, $cellAlignCenter);
            $table->addCell(1700, $cellRowSpan)->addText($salesGoods['name'], null, $cellAlignCenter);
            foreach ($salesGoods['visit'] as $visitId => $visit) {
                $piecesName = cons()->valueLang('goods.pieces', $visit['order_pieces']);
                $table->addRow();
                $table->addCell(null, $cellRowContinue);
                $table->addCell(null, $cellRowContinue);
                $table->addCell(1000, $cellVAlignCenter)->addText($visit['time'] . ($visitId ? '(拜访)' : '(自主)'),
                    null, $cellAlignCenter);
                $table->addCell(1000, $cellVAlignCenter)->addText($visit['stock'], null, $cellAlignCenter);
                $table->addCell(1000, $cellVAlignCenter)->addText($visit['production_date'], null, $cellAlignCenter);
                $table->addCell(1000,
                    $cellVAlignCenter)->addText($visit['order_price'] . '/' . $piecesName, null, $cellAlignCenter);
                $table->addCell(1000,
                    $cellVAlignCenter)->addText($visit['order_num'] . $piecesName, null, $cellAlignCenter);
                $table->addCell(1000, $cellVAlignCenter)->addText($visit['order_amount'], null, $cellAlignCenter);
                $table->addCell(1000, $cellVAlignCenter)->addText($visit['return_num'], null, $cellAlignCenter);
                $table->addCell(1000, $cellVAlignCenter)->addText($visit['return_amount'], null, $cellAlignCenter);
            }
        }

        $section = $phpWord->addSection();
        $table = $section->addTable($tableBolder);

        $table->addRow();
        $table->addCell(10500, $gridSpan10)->addText('赠品列表', null, $cellAlignCenter);

        $table->addRow();
        $table->addCell(1200, $cellVAlignCenter)->addText('商品ID', null, $cellAlignCenter);
        $table->addCell(4500, $cellVAlignCenter)->addText('商品名称', null, $cellAlignCenter);
        $table->addCell(1200, $cellVAlignCenter)->addText('订单ID', null, $cellAlignCenter);
        $table->addCell(2100, $cellVAlignCenter)->addText('下单时间', null, $cellAlignCenter);
        $table->addCell(1500, $cellVAlignCenter)->addText('数量', null, $cellAlignCenter);

        foreach ($result['gifts'] as $goodsId => $gift) {
            foreach ($gift['describe'] as $key => $describe) {
                $table->addRow();
                if ($key == 0) {
                    $table->addCell(1000, $cellRowSpan)->addText($goodsId, null, $cellAlignCenter);
                    $table->addCell(1000, $cellRowSpan)->addText($gift['name'], null, $cellAlignCenter);
                    $table->addCell(1000, $cellVAlignCenter)->addText($describe['order_id'], null, $cellAlignCenter);
                    $table->addCell(1000, $cellVAlignCenter)->addText($describe['time'], null, $cellAlignCenter);
                    $table->addCell(1000,
                        $cellVAlignCenter)->addText($describe['num'] . cons()->valueLang('goods.pieces',
                            $describe['pieces']), null, $cellAlignCenter);
                } else {
                    $table->addCell(null, $cellRowContinue);
                    $table->addCell(null, $cellRowContinue);
                    $table->addCell(1000, $cellVAlignCenter)->addText($describe['order_id'], null, $cellAlignCenter);
                    $table->addCell(1000, $cellVAlignCenter)->addText($describe['time'], null, $cellAlignCenter);
                    $table->addCell(1000,
                        $cellVAlignCenter)->addText($describe['num'] . cons()->valueLang('goods.pieces',
                            $describe['pieces']), null, $cellAlignCenter);
                }
            }
        }

        $name = $result['customer']->name . $result['beginTime'] . ' 至 ' . $result['endTime'] . '销售明细.docx';
        $phpWord->save(iconv('UTF-8', 'GBK//IGNORE', $name), 'Word2007', true);

    }

    /**
     * 库存查询记录导出
     *
     * @param $goods
     * @param $excelName
     */
    public function stockQueryExport($goods, $excelName)
    {
        Excel::create($excelName, function (LaravelExcelWriter $excel) use ($goods) {
            $excel->sheet('客户库存剩余', function (LaravelExcelWorksheet $sheet) use ($goods) {
                // Set auto size for sheet
                $sheet->setAutoSize(true);
                //标题
                $titles = [
                    '商品名称',
                    '商品条形码',
                    '当前实时库存',
                ];
                $sheet->setWidth(array(
                    'A' => 50,
                    'B' => 20,
                    'C' => 20,
                ));
                $goodsData = [];
                foreach ($goods as $item) {
                    $goodsData[] = [
                        $item->name,
                        $item->bar_code,
                        $item->surplus_inventory
                    ];
                }
                $sheetData = array_merge([$titles], $goodsData);
                $sheet->rows($sheetData);
                //单元格居中
                $sheet->cells('A1:C' . (count($goodsData) + 1), function (CellWriter $cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
            });
        })->export('xls');
    }
}
