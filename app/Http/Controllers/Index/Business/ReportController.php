<?php

namespace App\Http\Controllers\Index\Business;

use App\Models\Goods;
use App\Services\BusinessService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Index\Controller;
use PhpOffice\PhpWord\PhpWord;

class ReportController extends Controller
{
    /**
     * 报告首页
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $shop = auth()->user()->shop;
        $startDate = $request->input('start_date', ((new Carbon)->startOfMonth()->toDateString()));
        $endDate = $request->input('end_date', ((new Carbon)->toDateString()));
        $endDateTemp = (new Carbon($endDate))->addDay()->toDateString();

        $salesmenOrderData = (new BusinessService())->getSalesmanOrders($shop, $startDate, $endDateTemp);

        return view('index.business.report', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'salesmen' => $salesmenOrderData
        ]);
    }

    /**
     * 业务员报告详情
     *
     * @param \Illuminate\Http\Request $request
     * @param $salesmanId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function show(Request $request, $salesmanId)
    {
        $shop = auth()->user()->shop;
        $salesman = $shop->salesmen()->find($salesmanId);
        if (is_null($salesman)) {
            return $this->error('业务员不存在');
        }

        $data = $request->all();

        $startDate = isset($data['start_date']) ? new Carbon($data['start_date']) : (new Carbon())->startOfMonth();
        $endDate = isset($data['end_date']) ? (new Carbon($data['end_date']))->endOfDay() : Carbon::now();


        //拜访记录
        $visits = $salesman->visits()->OfTime($startDate, $endDate)->with([
            'orders.orderGoods',
            'goodsRecord',
            'salesmanCustomer.shippingAddress'
        ])->get();

        return view('index.business.report-detail', [
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'visitData' => $this->_formatVisit($visits),
            'salesman' => $salesman,

        ]);

    }

    /**
     * 报告详情导出
     *
     * @param \Illuminate\Http\Request $request
     * @param $salesmanId
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function export(Request $request, $salesmanId)
    {
        $shop = auth()->user()->shop;
        $salesman = $shop->salesmen()->find($salesmanId);
        if (is_null($salesman)) {
            return $this->error('业务员不存在');
        }

        $data = $request->all();

        $startDate = isset($data['start_date']) ? new Carbon($data['start_date']) : (new Carbon())->startOfMonth();
        $endDate = isset($data['end_date']) ? (new Carbon($data['end_date']))->endOfDay() : Carbon::now();


        //拜访记录
        $visits = $salesman->visits()->OfTime($startDate, $endDate)->with([
            'orders.orderGoods',
            'goodsRecord',
            'salesmanCustomer.shippingAddress'
        ])->get();
        $visitData = $this->_formatVisit($visits);

        $startDate = $startDate->toDateString();
        $endDate = $endDate->toDateString();
        $startDate == $endDate ? $this->_exportByDay($salesman, $startDate,
            $visitData) : $this->_exportByDate($salesman, $startDate, $endDate, $visitData);

    }

    /**
     * 业务员报告按天导出
     *
     * @param $salesman
     * @param $startDate
     * @param $endDate
     * @param $visitData
     */
    private function _exportByDay($salesman, $startDate, $visitData)
    {
        // Creating the new document...
        $phpWord = new PhpWord();

        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');

        $cellAlignRight = array('align' => 'right');
        $cellAlignCenter = array('align' => 'center');
        $cellVAlignCenter = array('valign' => 'center');
        $gridSpan9 = ['gridSpan' => 9];
        $gridSpan3 = ['gridSpan' => 3];
        $gridSpan4 = ['gridSpan' => 4];
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');

        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(10);
        $phpWord->addTableStyle('table', $styleTable);

        $phpWord->addParagraphStyle('Normal', [
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'lineHeight' => 1.2,  // 行间距
        ]);

        foreach ($visitData as $customerId => $visit) {
            $section = $phpWord->addSection();
            $table = $section->addTable('table');

            $table->addRow();
            $table->addCell(10500, $gridSpan9)->addText($startDate, ['size' => 30], $cellAlignCenter);

            $table->addRow();
            $table->addCell(10500, $gridSpan9)->addText($salesman->name . ' - 业务报告', null, $cellAlignCenter);

            $table->addRow();
            $table->addCell(1000)->addText('序号', null, $cellAlignCenter);
            $table->addCell(1500)->addText('时间', null, $cellAlignCenter);
            $table->addCell(1000)->addText('客户', null, $cellAlignCenter);
            $table->addCell(1500)->addText('店铺名称', null, $cellAlignCenter);
            $table->addCell(1000)->addText('联系人', null, $cellAlignCenter);
            $table->addCell(1500)->addText('联系电话', null, $cellAlignCenter);
            $table->addCell(3000, $gridSpan3)->addText('营业地址', null, $cellAlignCenter);

            $table->addRow();
            $table->addCell(1000)->addText($visit['visit_id'], null, $cellAlignCenter);
            $table->addCell(1500)->addText($visit['created_at'], null, $cellAlignCenter);
            $table->addCell(1000)->addText($customerId, null, $cellAlignCenter);
            $table->addCell(1500)->addText($visit['customer_name'], null, $cellAlignCenter);
            $table->addCell(1000)->addText($visit['contact'], null, $cellAlignCenter);
            $table->addCell(1500)->addText($visit['contact_information'], null, $cellAlignCenter);
            $table->addCell(3000, $gridSpan3)->addText($visit['shipping_address_name'], null, $cellAlignCenter);

            if (isset($visit['display_fee'])) {
                $table->addRow();
                $table->addCell(10500, $gridSpan9)->addText('陈列费', ['size' => 20], $cellAlignCenter);

                $table->addRow();
                $table->addCell(10500, $gridSpan9)->addText('现金 ：' . $visit['display_fee'][0]['display_fee'], null,
                    $cellAlignCenter);
            }

            if (isset($visit['mortgage'])) {
                $table->addRow();
                $table->addCell(10500, $gridSpan9)->addText('货抵', ['size' => 20], $cellAlignCenter);

                $table->addRow();
                $table->addCell(3500, $gridSpan3)->addText('商品名称', null, $cellAlignCenter);
                $table->addCell(4000, $gridSpan3)->addText('商品单位', null, $cellAlignCenter);
                $table->addCell(3000, $gridSpan3)->addText('数量', null, $cellAlignCenter);

                foreach (head($visit['mortgage']) as $mortgage) {
                    $table->addRow();
                    $table->addCell(3500, $gridSpan3)->addText($mortgage['name'], null, $cellAlignCenter);
                    $table->addCell(4000, $gridSpan3)->addText($mortgage['pieces'], null, $cellAlignCenter);
                    $table->addCell(3000, $gridSpan3)->addText($mortgage['num'], null, $cellAlignCenter);
                }
            }

            $table->addRow();
            $table->addCell(10500, $gridSpan9)->addText('客户销售商品', ['size' => 20], $cellAlignCenter);

            $table->addRow();
            $table->addCell(1000)->addText('商品ID', null, $cellAlignCenter);
            $table->addCell(1500)->addText('商品名称', null, $cellAlignCenter);
            $table->addCell(1000)->addText('商品库存', null, $cellAlignCenter);
            $table->addCell(1500)->addText('生产日期', null, $cellAlignCenter);
            $table->addCell(1000)->addText('商品单价', null, $cellAlignCenter);
            $table->addCell(1500)->addText('订货数量', null, $cellAlignCenter);
            $table->addCell(1000)->addText('订货总金额', null, $cellAlignCenter);
            $table->addCell(1000)->addText('退货数量', null, $cellAlignCenter);
            $table->addCell(1000)->addText('退货总金额', null, $cellAlignCenter);

            foreach ($visit['statistics'] as $goodsId => $statistics) {
                $table->addRow();
                $table->addCell(1000)->addText($goodsId, null, $cellAlignCenter);
                $table->addCell(1500)->addText($statistics['goods_name'], null, $cellAlignCenter);
                $table->addCell(1000)->addText($statistics['stock'], null, $cellAlignCenter);
                $table->addCell(1500)->addText($statistics['production_date'], null, $cellAlignCenter);
                $table->addCell(1000)->addText((isset($statistics['price']) ? $statistics['price'] : 0) . '/' . (isset($statistics['pieces']) ? $statistics['pieces'] : '件'),
                    null, $cellAlignCenter);
                $table->addCell(1500)->addText($statistics['order_num'], null, $cellAlignCenter);
                $table->addCell(1000)->addText($statistics['order_amount'], null, $cellAlignCenter);
                $table->addCell(1000)->addText($statistics['return_order_num'], null, $cellAlignCenter);
                $table->addCell(1000)->addText($statistics['return_amount'], null, $cellAlignCenter);
            }
            $table->addRow();
            $table->addCell(10500, $gridSpan9)->addText("订货总金额：{$visit['amount']}   退货总金额：{$visit['return_amount']}",
                ['size' => 20], $cellAlignCenter);
        }
        $name = $salesman->name . $startDate . '业务报告.docx';
        $phpWord->save($name, 'Word2007', true);
    }

    /**
     * 业务员报告按天导出
     *
     * @param $salesman
     * @param $startDate
     * @param $endDate
     * @param $visitData
     */
    private function _exportByDate($salesman, $startDate, $endDate, $visitData)
    {

        // Creating the new document...
        $phpWord = new PhpWord();

        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');

        $cellAlignRight = array('align' => 'right');
        $cellAlignCenter = array('align' => 'center');
        $cellVAlignCenter = array('valign' => 'center');
        $gridSpan9 = ['gridSpan' => 9];
        $gridSpan3 = ['gridSpan' => 3];
        $gridSpan4 = ['gridSpan' => 4];
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');

        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(10);
        $phpWord->addTableStyle('table', $styleTable);

        $phpWord->addParagraphStyle('Normal', [
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'lineHeight' => 1.2,  // 行间距
        ]);

        foreach ($visitData as $customerId => $visit) {
            $section = $phpWord->addSection();
            $table = $section->addTable('table');

            $table->addRow();
            $table->addCell(10500, $gridSpan9)->addText($startDate . ' 至 ' . $endDate, ['size' => 30], $cellAlignCenter);

            $table->addRow();
            $table->addCell(10500, $gridSpan9)->addText($salesman->name . ' - 业务报告', null, $cellAlignCenter);
        }
    }

    /**
     * 格式化访问数据
     *
     * @param $visits
     * @return array
     */
    private function _formatVisit($visits)
    {
        $orderConf = cons('salesman.order');

        $visitData = [];

        foreach ($visits as $visit) {
            $customerId = $visit->salesman_customer_id;

            //拜访客户信息
            $visitData[$customerId]['visit_id'] = $visit->id;
            $visitData[$customerId]['created_at'] = $visit->created_at;
            $visitData[$customerId]['customer_name'] = $visit->salesmanCustomer->name;
            $visitData[$customerId]['contact'] = $visit->salesmanCustomer->contact;
            $visitData[$customerId]['contact_information'] = $visit->salesmanCustomer->contact_information;
            $visitData[$customerId]['shipping_address_name'] = $visit->salesmanCustomer->shipping_address_name;

            $orderForm = $visit->orders->filter(function ($item) use ($orderConf) {
                return !is_null($item) && $item->type == $orderConf['type']['order'];
            })->first();

            if (!is_null($orderForm)) {
                $orderForm->display_fee && ($visitData[$customerId]['display_fee'][] = [
                    'created_at' => $orderForm->created_at,
                    'display_fee' => $orderForm->display_fee
                ]);
            }

            //拜访商品记录
            $goodsRecodeData = [];
            foreach ($visit->goodsRecord as $record) {
                if (!is_null($record)) {
                    $goodsRecodeData[$record->goods_id] = $record;
                }
            }

            $orderGoods = [];


            $visitData[$customerId]['amount'] = 0;
            $visitData[$customerId]['return_amount'] = 0;
            $mortgageGoods = [];

            $allGoods = $visit->orders->pluck('orderGoods')->collapse();
            $goodsIds = $allGoods->pluck('goods_id')->all();

            $goodsNames = Goods::whereIn('id', $goodsIds)->lists('name', 'id');

            foreach ($allGoods as $goods) {
                $orderGoods[$goods->type][] = $goods;

                if ($goods->type == $orderConf['goods']['type']['order']) {
                    $visitData[$customerId]['statistics'][$goods->goods_id]['order_num'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['order_num']) ? $visitData[$customerId]['statistics'][$goods->goods_id]['order_num'] + $goods->num : $goods->num;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['order_amount']) ? bcadd($visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'],
                        $goods->amount, 2) : $goods->amount;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['price'] = $goods->price;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['pieces'] = cons()->valueLang('goods.pieces',
                        $goods->pieces);
                } elseif ($goods->type == $orderConf['goods']['type']['return']) {
                    $visitData[$customerId]['statistics'][$goods->goods_id]['return_order_num'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['return_order_num']) ? $visitData[$customerId]['statistics'][$goods->goods_id]['return_order_num'] + intval($goods->num) : intval($goods->num);
                    $visitData[$customerId]['statistics'][$goods->goods_id]['return_amount'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['return_amount']) ? bcadd($visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'],
                        $goods->amount, 2) : $goods->amount;
                }
                if ($goods->type == $orderConf['goods']['type']['mortgage']) {
                    $mortgageGoods[] = $goods;
                } else {
                    $visitData[$customerId]['statistics'][$goods->goods_id]['goods_name'] = $goodsNames[$goods->goods_id];
                    $visitData[$customerId]['statistics'][$goods->goods_id]['stock'] = isset($goodsRecodeData[$goods->goods_id]) ? $goodsRecodeData[$goods->goods_id]->stock : 0;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['production_date'] = isset($goodsRecodeData[$goods->goods_id]) ? $goodsRecodeData[$goods->goods_id]->production_date : 0;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['order_amount']) ? $visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'] : 0;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['return_amount'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['return_amount']) ? $visitData[$customerId]['statistics'][$goods->goods_id]['return_amount'] : 0;
                    $visitData[$customerId]['amount'] = bcadd($visitData[$customerId]['amount'],
                        $visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'], 2);
                    $visitData[$customerId]['return_amount'] = bcadd($visitData[$customerId]['return_amount'],
                        $visitData[$customerId]['statistics'][$goods->goods_id]['return_amount'], 2);
                }
            }

            //货抵商品
            foreach ($mortgageGoods as $mortgage) {
                $date = (new Carbon($mortgage->created_at))->toDateString();
                $visitData[$customerId]['mortgage'][$date][] = [
                    'name' => $goodsNames[$mortgage->goods_id],
                    'num' => $mortgage->num,
                    'pieces' => cons()->valueLang('goods.pieces', $mortgage->pieces)
                ];
            }

        }

        return $visitData;
    }

}
