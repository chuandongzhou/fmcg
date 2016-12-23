<?php

namespace App\Http\Controllers\Index\Business;

use App\Models\Goods;
use App\Services\BusinessService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Index\Controller;
use PhpOffice\PhpWord\Element\Table;
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

        $startDate = isset($data['start_date']) ? new Carbon($data['start_date']) : (new Carbon())->startOfDay();
        $endDate = isset($data['end_date']) ? (new Carbon($data['end_date']))->endOfDay() : Carbon::now();


        //拜访记录
        $visits = $salesman->visits()->OfTime($startDate, $endDate)->with([
            'orders.orderGoods.goods',
            'orders.displayList.mortgageGoods',
            'goodsRecord.goods',
            'salesmanCustomer.shippingAddress'
        ])->get();

        //自主下单订单

        $platFormOrders = $salesman->orders()->where('salesman_visit_id', 0)->OfData([
            'start_date' => $startDate,
            'end_date' => $endDate
        ])->with('order.orderGoods.goods')->get();

        $startDate = $startDate->toDateString();
        $endDate = $endDate->toDateString();

        $viewName = $startDate == $endDate ? 'report-day-detail' : 'report-date-detail';

        $businessService = new BusinessService();
        $visitFormat = $businessService->formatVisit($visits);

        $platFormOrders = $businessService->formatOrdersByCustomer($platFormOrders);


        return view('index.business.' . $viewName, [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'visitData' => $visitFormat['visitData'],
            'visitStatistics' => $visitFormat['visitStatistics'],
            'platFormOrders' => $platFormOrders,
            'platFormOrdersList' => $platFormOrders->pluck('orders')->collapse(),
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
            'orders.mortgageGoods',
            'goodsRecord',
            'salesmanCustomer.shippingAddress'
        ])->get();

        $businessService = new BusinessService();

        $visitFormat = $businessService->formatVisit($visits);

        //自主下单订单

        $platFormOrders = $salesman->orders()->where('salesman_visit_id', 0)->OfData([
            'start_date' => $startDate,
            'end_date' => $endDate
        ])->with('order.orderGoods.goods')->get();

        $platFormOrders = $businessService->formatOrdersByCustomer($platFormOrders);

        $startDate = $startDate->toDateString();
        $endDate = $endDate->toDateString();
       /* $startDate == $endDate ? $this->_exportByDay($salesman, $startDate, $visitFormat,
            $platFormOrders) :*/ $this->_exportByDate($salesman, $startDate, $endDate, $visitFormat, $platFormOrders);

    }

    /**
     * 报告列表导出
     *
     * @param \Illuminate\Http\Request $request
     */
    public function exportIndex(Request $request)
    {
        $shop = auth()->user()->shop;
        $startDate = $request->input('start_date', ((new Carbon)->startOfMonth()->toDateString()));
        $endDate = $request->input('end_date', ((new Carbon)->toDateString()));
        $endDateTemp = (new Carbon($endDate))->addDay()->toDateString();

        $salesmenOrderData = (new BusinessService())->getSalesmanOrders($shop, $startDate, $endDateTemp);

        $phpWord = new PhpWord();

        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');


        $cellAlignCenter = ['align' => 'center'];
        $cellRowSpan = ['vMerge' => 'restart', 'valign' => 'center'];
        $cellRowContinue = array('vMerge' => 'continue');

        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(10);
        $phpWord->addTableStyle('table', $styleTable);

        $phpWord->addParagraphStyle('Normal', [
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'lineHeight' => 1.2,  // 行间距
        ]);

        $section = $phpWord->addSection();
        foreach ($salesmenOrderData as $man) {
            $table = $section->addTable('table');
            $table->addRow();
            $table->addCell(2000, $cellRowSpan)->addText($man->name, null, $cellAlignCenter);
            $table->addCell(1500)->addText('拜访客户数', null, $cellAlignCenter);
            $table->addCell(1500)->addText('订货单数(拜访+自主)', null, $cellAlignCenter);
            $table->addCell(1500)->addText('订货总金额(拜访+自主)', null, $cellAlignCenter);
            $table->addCell(1500)->addText('退货单数', null, $cellAlignCenter);
            $table->addCell(1500)->addText('退货总金额', null, $cellAlignCenter);

            $table->addRow();
            $table->addCell(2000, $cellRowContinue);
            $table->addCell(1500)->addText($man->visitCustomerCount, null, $cellAlignCenter);
            $table->addCell(1500)->addText($man->orderFormCount . '(' . $man->visitOrderFormCount . '+' . bcsub($man->orderFormCount,
                    $man->visitOrderFormCount) . ')', null, $cellAlignCenter);
            $table->addCell(1500)->addText($man->orderFormSumAmount . '(' . $man->visitOrderFormSumAmount . '+' . ($man->orderFormSumAmount - $man->visitOrderFormSumAmount) . ')',
                null, $cellAlignCenter);
            $table->addCell(1500)->addText($man->returnOrderCount, null, $cellAlignCenter);
            $table->addCell(1500)->addText($man->returnOrderSumAmount, null, $cellAlignCenter);
        }
        $name = $startDate . '至' . $endDate . '业务报表.docx';
        $phpWord->save(iconv('UTF-8', 'GBK//IGNORE', $name), 'Word2007', true);

    }

    /**
     * 业务员报告按天导出
     *
     * @param $salesman
     * @param $startDate
     * @param $visitFormat
     * @param $platFormOrders
     *
     */
    private function _exportByDay($salesman, $startDate, $visitFormat, $platFormOrders = [])
    {
        // Creating the new document...
        $phpWord = new PhpWord();

        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');


        $cellAlignCenter = ['align' => 'center'];
        $gridSpan9 = ['gridSpan' => 9, 'valign' => 'center'];
        $gridSpan3 = ['gridSpan' => 3, 'valign' => 'center'];
        $gridSpan6 = ['gridSpan' => 6, 'valign' => 'center'];

        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(10);
        $phpWord->addTableStyle('table', $styleTable);

        $phpWord->addParagraphStyle('Normal', [
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'lineHeight' => 1.2,  // 行间距
        ]);

        $visitData = $visitFormat['visitData'];
        $visitStatistics = $visitFormat['visitStatistics'];


        foreach ($visitData as $customerId => $visit) {
            $section = $phpWord->addSection();
            $table = $section->addTable('table');
            if ($visit == head($visitData)) {
                $table->addRow();
                $table->addCell(10500, $gridSpan9)->addText($startDate, ['size' => 30], $cellAlignCenter);

                $table->addRow();
                $table->addCell(10500, $gridSpan9)->addText($salesman->name . ' - 业务报表', ['size' => 16],
                    $cellAlignCenter);

                $this->_exportStatistics($table, $visitStatistics, $visitData, $platFormOrders);
            }
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
                $table->addCell(0, $gridSpan3)->addText('月份 ', null, $cellAlignCenter);
                $table->addCell(0, $gridSpan6)->addText('现金 ', null, $cellAlignCenter);

                foreach ($visit['display_fee'] as $item) {
                    $table->addRow();
                    $table->addCell(0, $gridSpan3)->addText($item['month'], null, $cellAlignCenter);
                    $table->addCell(0, $gridSpan6)->addText($item['display_fee'], null, $cellAlignCenter);
                }


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
                    $table->addCell(4000, $gridSpan3)->addText(cons()->valueLang('goods.pieces', $mortgage['pieces']),
                        null, $cellAlignCenter);
                    $table->addCell(3000, $gridSpan3)->addText($mortgage['num'], null, $cellAlignCenter);
                }
            }

            if (isset($visit['statistics'])) {
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
                    $table->addCell(1000)->addText((isset($statistics['price']) ? $statistics['price'] : 0) . '/' . (isset($statistics['pieces']) ? cons()->valueLang('goods.pieces',
                            $statistics['pieces']) : '件'),
                        null, $cellAlignCenter);
                    $table->addCell(1500)->addText($statistics['order_num'], null, $cellAlignCenter);
                    $table->addCell(1000)->addText($statistics['order_amount'], null, $cellAlignCenter);
                    $table->addCell(1000)->addText($statistics['return_order_num'], null, $cellAlignCenter);
                    $table->addCell(1000)->addText($statistics['return_amount'], null, $cellAlignCenter);
                }
                $table->addRow();
                $table->addCell(10500,
                    $gridSpan9)->addText("订货总金额：{$visit['amount']}   退货总金额：{$visit['return_amount']}",
                    ['size' => 20], $cellAlignCenter);
            }
        }

        $this->_exportPlatformOrders($phpWord, $platFormOrders);

        $name = $salesman->name . $startDate . '业务报表明细.docx';
        $phpWord->save(iconv('UTF-8', 'GBK//IGNORE', $name), 'Word2007', true);
    }

    /**
     * 业务员报告按天导出
     *
     * @param $salesman
     * @param $startDate
     * @param $endDate
     * @param $visitFormat
     * @param $platFormOrders
     */
    private function _exportByDate($salesman, $startDate, $endDate, $visitFormat, $platFormOrders = [])
    {

        // Creating the new document...
        $phpWord = new PhpWord();

        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');

        $cellAlignCenter = ['align' => 'center'];
        $cellVAlignCenter = ['valign' => 'center'];
        $gridSpan9 = ['gridSpan' => 9, 'valign' => 'center'];
        $gridSpan2 = ['gridSpan' => 2, 'valign' => 'center'];
        $gridSpan3 = ['gridSpan' => 3, 'valign' => 'center'];
        $gridSpan5 = ['gridSpan' => 5, 'valign' => 'center'];
        $gridSpan4 = ['gridSpan' => 4, 'valign' => 'center'];
        $cellRowSpan = ['vMerge' => 'restart', 'valign' => 'center'];
        $cellRowContinue = array('vMerge' => 'continue');

        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(10);
        $phpWord->addTableStyle('table', $styleTable);

        $phpWord->addParagraphStyle('Normal', [
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'lineHeight' => 1.2,  // 行间距
        ]);

        $visitData = $visitFormat['visitData'];
        $visitStatistics = $visitFormat['visitStatistics'];


        foreach ($visitData as $customerId => $visit) {
            $section = $phpWord->addSection();
            $table = $section->addTable('table');

            if ($visit == head($visitData)) {
                $table->addRow();
                $table->addCell(10500, $gridSpan9)->addText($startDate == $endDate ?$startDate:$startDate . ' 至 ' . $endDate, ['size' => 30],
                    $cellAlignCenter);

                $table->addRow();
                $table->addCell(10500, $gridSpan9)->addText($salesman->name . ' - 业务报表', ['size' => 16],
                    $cellAlignCenter);

                $this->_exportStatistics($table, $visitStatistics, $visitData, $platFormOrders->pluck('orders')->collapse());
            }
            $table->addRow();
            $table->addCell(10500, $gridSpan9)->addText('客戶信息', null,
                $cellAlignCenter);

            $table->addRow();
            $table->addCell(1200, $gridSpan2)->addText('客户编号', null, $cellAlignCenter);
            $table->addCell(2000, $gridSpan2)->addText('店铺名称', null, $cellAlignCenter);
            $table->addCell(2000, $gridSpan2)->addText('联系人', null, $cellAlignCenter);
            $table->addCell(1800, $gridSpan2)->addText('联系电话', null, $cellAlignCenter);
            $table->addCell(3500, $cellVAlignCenter)->addText('营业地址', null, $cellAlignCenter);

            $table->addRow();
            $table->addCell(1200, $gridSpan2)->addText($customerId, null, $cellAlignCenter);
            $table->addCell(2000, $gridSpan2)->addText($visit['customer_name'], null, $cellAlignCenter);
            $table->addCell(2000, $gridSpan2)->addText($visit['contact'], null, $cellAlignCenter);
            $table->addCell(1800, $gridSpan2)->addText($visit['contact_information'], null, $cellAlignCenter);
            $table->addCell(3500, $cellVAlignCenter)->addText($visit['shipping_address_name'], null, $cellAlignCenter);


            if($startDate == $endDate){
                $table->addRow();
                $table->addCell(3500, $gridSpan5)->addText('订货总金额', null, $cellAlignCenter);
                $table->addCell(3500, $gridSpan4)->addText('退货总金额', null, $cellAlignCenter);
                $table->addRow();
                $table->addCell(3500, $gridSpan5)->addText($visit['amount'], null, $cellAlignCenter);
                $table->addCell(3500, $gridSpan4)->addText($visit['return_amount'], null, $cellAlignCenter);
            }else{
                $table->addRow();
                $table->addCell(3500, $gridSpan3)->addText('拜访次数', null, $cellAlignCenter);
                $table->addCell(3500, $gridSpan3)->addText('订货总金额', null, $cellAlignCenter);
                $table->addCell(3500, $gridSpan3)->addText('退货总金额', null, $cellAlignCenter);
                $table->addRow();
                $table->addCell(3500, $gridSpan3)->addText($visit['visit_count'], null, $cellAlignCenter);
                $table->addCell(3500, $gridSpan3)->addText($visit['amount'], null, $cellAlignCenter);
                $table->addCell(3500, $gridSpan3)->addText($visit['return_amount'], null, $cellAlignCenter);

            }


            if (isset($visit['display_fee'])) {
                $table->addRow();
                $table->addCell(10500, $gridSpan9)->addText('陈列费（现金）', null,
                    $cellAlignCenter);


                $table->addRow();
                $table->addCell(2000, $gridSpan3)->addText('月份', null, $cellAlignCenter);
                $table->addCell(2000, $gridSpan3)->addText('拜访时间', null, $cellAlignCenter);
                $table->addCell(5300, $gridSpan3)->addText('金额', null, $cellAlignCenter);

                foreach ($visit['display_fee'] as $displayFee) {
                    $table->addRow();
                    $table->addCell(2000, $gridSpan3)->addText($displayFee['month'], null, $cellAlignCenter);
                    $table->addCell(2000, $gridSpan3)->addText($displayFee['created_at'], null, $cellAlignCenter);
                    $table->addCell(5300, $gridSpan3)->addText($displayFee['display_fee'], null, $cellAlignCenter);
                }
            }
            if (isset($visit['mortgage'])) {
                $table->addRow();
                $table->addCell(10500, $gridSpan9)->addText('陈列费（货抵）', null,
                    $cellAlignCenter);

                $table->addRow();
                $table->addCell(2000, $gridSpan2)->addText('月份', null, $cellAlignCenter);
                $table->addCell(2000, $gridSpan2)->addText('拜访时间', null, $cellAlignCenter);
                $table->addCell(2000, $gridSpan2)->addText('商品名称', null, $cellAlignCenter);
                $table->addCell(1800, $gridSpan2)->addText('商品单位', null, $cellAlignCenter);
                $table->addCell(3500, $cellVAlignCenter)->addText('商品数量', null, $cellAlignCenter);
                foreach ($visit['mortgage'] as $date => $mortgages) {
                    $table->addRow();
                    $table->addCell(2000, array_merge($gridSpan2,$cellRowSpan))->addText($date, null, $cellAlignCenter);
                    foreach ($mortgages as $mortgage) {
                        $table->addRow();
                        $table->addCell(null, array_merge($gridSpan2,$cellRowContinue));
                        $table->addCell(2000, $gridSpan2)->addText($mortgage['created_at'], null, $cellAlignCenter);
                        $table->addCell(2000, $gridSpan2)->addText($mortgage['name'], null, $cellAlignCenter);
                        $table->addCell(1800, $gridSpan2)->addText(cons()->valueLang('goods.pieces',
                            $mortgage['pieces']), null, $gridSpan2);
                        $table->addCell(3500, $cellAlignCenter)->addText($mortgage['num'], null, $cellAlignCenter);
                    }
                }
            }
            if (isset($visit['statistics'])) {
                $table->addRow();
                $table->addCell(10500, $gridSpan9)->addText('销售统计', null, $cellAlignCenter);

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
                    $table->addCell(1000)->addText((isset($statistics['price']) ? $statistics['price'] : 0) . '/' . (isset($statistics['pieces']) ? cons()->valueLang('goods.pieces',
                            $statistics['pieces']) : '件'),
                        null, $cellAlignCenter);
                    $table->addCell(1500)->addText($statistics['order_num'], null, $cellAlignCenter);
                    $table->addCell(1000)->addText($statistics['order_amount'], null, $cellAlignCenter);
                    $table->addCell(1000)->addText($statistics['return_order_num'], null, $cellAlignCenter);
                    $table->addCell(1000)->addText($statistics['return_amount'], null, $cellAlignCenter);
                }


            }
        }

        $this->_exportPlatformOrders($phpWord, $platFormOrders);

        $name = $startDate == $endDate?$salesman->name.$startDate . '业务报表明细.docx':$salesman->name . $startDate . '至' . $endDate . '业务报表明细.docx';
        $phpWord->save(iconv('UTF-8', 'GBK//IGNORE', $name), 'Word2007', true);
    }

    /**
     * 导出统计
     *
     * @param \PhpOffice\PhpWord\Element\Table $table
     * @param $visitStatistics
     * @param $visitData
     * @param $platFormOrdersList
     */
    private function _exportStatistics(Table $table, $visitStatistics, $visitData, $platFormOrdersList)
    {
        $cellAlignCenter = ['align' => 'center'];
        $table->addRow();
        $table->addCell(1000)->addText('拜访客户数', null, $cellAlignCenter);
        $table->addCell(1500)->addText('退货单数', null, $cellAlignCenter);
        $table->addCell(1000)->addText('退货金额', null, $cellAlignCenter);
        $table->addCell(1500)->addText('拜访订货单数', null, $cellAlignCenter);
        $table->addCell(1000)->addText('拜访订货金额', null, $cellAlignCenter);
        $table->addCell(1500)->addText('自主订货单数', null, $cellAlignCenter);
        $table->addCell(1000)->addText('自主订货金额', null, $cellAlignCenter);
        $table->addCell(1000)->addText('总订货单数', null, $cellAlignCenter);
        $table->addCell(1000)->addText('总订货金额', null, $cellAlignCenter);

        $table->addRow();
        $table->addCell(1000)->addText( count($visitData), null, $cellAlignCenter);
        $table->addCell(1500)->addText((isset($visitStatistics['return_order_count']) ? $visitStatistics['return_order_count'] : 0), null, $cellAlignCenter);
        $table->addCell(1000)->addText((isset($visitStatistics['return_order_amount']) ? $visitStatistics['return_order_amount'] : 0), null, $cellAlignCenter);
        $table->addCell(1500)->addText((isset($visitStatistics['order_form_count']) ? $visitStatistics['order_form_count'] : 0), null, $cellAlignCenter);
        $table->addCell(1000)->addText((isset($visitStatistics['order_form_amount']) ? $visitStatistics['order_form_amount'] : 0), null, $cellAlignCenter);
        $table->addCell(1500)->addText($platFormOrdersList->count(), null, $cellAlignCenter);
        $table->addCell(1000)->addText( $platFormOrdersList->sum('amount'), null, $cellAlignCenter);
        $table->addCell(1000)->addText((isset($visitStatistics['order_form_count']) ? $visitStatistics['order_form_count'] + $platFormOrdersList->count() : $platFormOrdersList->count()), null, $cellAlignCenter);
          $table->addCell(1000)->addText((isset($visitStatistics['order_form_amount']) ? bcadd($visitStatistics['order_form_amount'],
                  $platFormOrdersList->sum('amount'), 2) : $platFormOrdersList->sum('amount')), null, $cellAlignCenter);


    }

    /**
     * 导出自主下单订单
     *
     * @param \PhpOffice\PhpWord\PhpWord $phpWord
     * @param $platformOrders
     */
    private function _exportPlatformOrders(PhpWord $phpWord, $platformOrders)
    {
        $cellAlignCenter = ['align' => 'center'];
        $cellVAlignCenter = ['valign' => 'center'];
        $gridSpan2 = ['gridSpan' => 2, 'valign' => 'center'];
        $gridSpan5 = ['gridSpan' => 5, 'valign' => 'center'];

        foreach ($platformOrders as $customer) {
            $section = $phpWord->addSection();
            $table = $section->addTable('table');

            if ($customer == $platformOrders->first()) {
                $table->addRow();
                $table->addCell(10500, $gridSpan5)->addText('自主下单订单', ['size' => 30], $cellAlignCenter);
            }
            $table->addRow();
            $table->addCell(2500, $cellVAlignCenter)->addText('客户编号', null, $cellAlignCenter);
            $table->addCell(2500, $cellVAlignCenter)->addText('店铺名称', null, $cellAlignCenter);
            $table->addCell(1000, $cellVAlignCenter)->addText('联系人', null, $cellAlignCenter);
            $table->addCell(2000, $cellVAlignCenter)->addText('电话号码', null, $cellAlignCenter);
            $table->addCell(2500, $cellVAlignCenter)->addText('营业地址', null, $cellAlignCenter);

            $table->addRow();
            $table->addCell(2500, $cellVAlignCenter)->addText($customer['number'], null, $cellAlignCenter);
            $table->addCell(2500, $cellVAlignCenter)->addText($customer['shop_name'], null, $cellAlignCenter);
            $table->addCell(1000, $cellVAlignCenter)->addText($customer['contact'], null, $cellAlignCenter);
            $table->addCell(2000, $cellVAlignCenter)->addText($customer['contact_information'], null, $cellAlignCenter);
            $table->addCell(2500, $cellVAlignCenter)->addText($customer['business_address'], null, $cellAlignCenter);

            $table->addRow();
            $table->addCell(2500, $cellVAlignCenter)->addText('订单ID', null, $cellAlignCenter);
            $table->addCell(3500, $gridSpan2)->addText('下单时间', null, $cellAlignCenter);
            $table->addCell(2000, $cellVAlignCenter)->addText('订单状态', null, $cellAlignCenter);
            $table->addCell(2500, $cellVAlignCenter)->addText('订单金额', null, $cellAlignCenter);

            foreach ($customer['orders'] as $order) {
                $table->addRow();
                $table->addCell(2500, $cellVAlignCenter)->addText($order->order_id, null, $cellAlignCenter);
                $table->addCell(3500, $gridSpan2)->addText($order->created_at, null, $cellAlignCenter);
                $table->addCell(2000, $cellVAlignCenter)->addText($order->order_status_name, null, $cellAlignCenter);
                $table->addCell(2500, $cellVAlignCenter)->addText($order->amount, null, $cellAlignCenter);
            }

            $table->addRow();
            $table->addCell(10500, $gridSpan5)->addText('总计：' . $customer['orders']->sum('amount'), null,
                $cellAlignCenter);

            $table->addRow();
            $table->addCell(5000, $gridSpan2)->addText('商品名称', null, $cellAlignCenter);
            $table->addCell(3000, $gridSpan2)->addText('订货数量', null, $cellAlignCenter);
            $table->addCell(2500, $cellVAlignCenter)->addText('订货金额', null, $cellAlignCenter);

            foreach ($customer['orderGoods'] as $orderGoods) {
                $table->addRow();
                $table->addCell(5000, $gridSpan2)->addText($orderGoods['name'], null, $cellAlignCenter);
                $table->addCell(3000, $gridSpan2)->addText($orderGoods['order_num'], null, $cellAlignCenter);
                $table->addCell(2500, $cellVAlignCenter)->addText($orderGoods['order_amount'], null, $cellAlignCenter);
            }

        }
    }

}
