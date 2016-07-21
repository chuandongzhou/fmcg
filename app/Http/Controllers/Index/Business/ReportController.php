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
            'orders.mortgageGoods',
            'goodsRecord',
            'salesmanCustomer.shippingAddress'
        ])->get();


        $startDate = $startDate->toDateString();
        $endDate = $endDate->toDateString();

        $viewName = $startDate == $endDate ? 'report-day-detail' : 'report-date-detail';

        $data = (new BusinessService())->formatVisit($visits);

        return view('index.business.' . $viewName, [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'visitData' => (new BusinessService())->formatVisit($visits),
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
        $visitData = (new BusinessService())->formatVisit($visits);

        $startDate = $startDate->toDateString();
        $endDate = $endDate->toDateString();
        $startDate == $endDate ? $this->_exportByDay($salesman, $startDate,
            $visitData) : $this->_exportByDate($salesman, $startDate, $endDate, $visitData);

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
            $table->addCell(1500)->addText('订货单数', null, $cellAlignCenter);
            $table->addCell(1500)->addText('订货总金额', null, $cellAlignCenter);
            $table->addCell(1500)->addText('退货单数', null, $cellAlignCenter);
            $table->addCell(1500)->addText('退货总金额', null, $cellAlignCenter);

            $table->addRow();
            $table->addCell(2000, $cellRowContinue);
            $table->addCell(1500)->addText($man->visitCustomerCount, null, $cellAlignCenter);
            $table->addCell(1500)->addText($man->orderFormCount, null, $cellAlignCenter);
            $table->addCell(1500)->addText($man->orderFormSumAmount, null, $cellAlignCenter);
            $table->addCell(1500)->addText($man->returnOrderCount, null, $cellAlignCenter);
            $table->addCell(1500)->addText($man->returnOrderSumAmount, null, $cellAlignCenter);
        }
        $name = $startDate . '至' . $endDate . '业务报告.docx';
        $phpWord->save($name, 'Word2007', true);

    }

    /**
     * 业务员报告按天导出
     *
     * @param $salesman
     * @param $startDate
     * @param $visitData
     */
    private function _exportByDay($salesman, $startDate, $visitData)
    {
        // Creating the new document...
        $phpWord = new PhpWord();

        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');


        $cellAlignCenter = ['align' => 'center'];
        $gridSpan9 = ['gridSpan' => 9, 'valign' => 'center'];
        $gridSpan3 = ['gridSpan' => 3, 'valign' => 'center'];

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
        $name = $salesman->name . $startDate . '业务报告明细.docx';
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

        $cellAlignCenter = ['align' => 'center'];
        $cellVAlignCenter = ['valign' => 'center'];
        $gridSpan6 = ['gridSpan' => 6, 'valign' => 'center'];
        $gridSpan2 = ['gridSpan' => 2, 'valign' => 'center'];
        $gridSpan4 = ['gridSpan' => 4, 'valign' => 'center'];
        $gridSpan5 = ['gridSpan' => 5, 'valign' => 'center'];
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

        foreach ($visitData as $customerId => $visit) {
            $section = $phpWord->addSection();
            $table = $section->addTable('table');

            $table->addRow();
            $table->addCell(10500, $gridSpan6)->addText($startDate . ' 至 ' . $endDate, ['size' => 30],
                $cellAlignCenter);

            $table->addRow();
            $table->addCell(10500, $gridSpan6)->addText($salesman->name . ' - 业务报告', null, $cellAlignCenter);

            $table->addRow();
            $table->addCell(1200, $cellVAlignCenter)->addText('客户编号', null, $cellAlignCenter);
            $table->addCell(2000, $cellVAlignCenter)->addText('店铺名称', null, $cellAlignCenter);
            $table->addCell(2000, $cellVAlignCenter)->addText('联系人', null, $cellAlignCenter);
            $table->addCell(1800, $cellVAlignCenter)->addText('联系电话', null, $cellAlignCenter);
            $table->addCell(3500, $gridSpan2)->addText('营业地址', null, $cellAlignCenter);

            $table->addRow();
            $table->addCell(1200, $cellVAlignCenter)->addText($customerId, null, $cellAlignCenter);
            $table->addCell(2000, $cellVAlignCenter)->addText($visit['customer_name'], null, $cellAlignCenter);
            $table->addCell(2000, $cellVAlignCenter)->addText($visit['contact'], null, $cellAlignCenter);
            $table->addCell(1800, $cellVAlignCenter)->addText($visit['contact_information'], null, $cellAlignCenter);
            $table->addCell(3500, $gridSpan2)->addText($visit['shipping_address_name'], null, $cellAlignCenter);


            $table->addRow();
            $table->addCell(1200, $cellRowSpan)->addText('陈列费', null, $cellAlignCenter);

            if (isset($visit['display_fee'])) {
                $table->addRow();
                $table->addCell(null, $cellRowContinue);
                $table->addCell(9300, $gridSpan5)->addText('现金', null, $cellAlignCenter);

                $table->addRow();
                $table->addCell(null, $cellRowContinue);
                $table->addCell(2000, $cellVAlignCenter)->addText('拜访时间', null, $cellAlignCenter);
                $table->addCell(7300, $gridSpan4)->addText('金额', null, $cellAlignCenter);

                foreach ($visit['display_fee'] as $displayFee) {
                    $table->addRow();
                    $table->addCell(null, $cellRowContinue);
                    $table->addCell(2000, $cellVAlignCenter)->addText($displayFee['created_at'], null,
                        $cellAlignCenter);
                    $table->addCell(7300, $gridSpan4)->addText($displayFee['display_fee'], null, $cellAlignCenter);
                }
            }
            if (isset($visit['mortgage'])) {
                $table->addRow();
                $table->addCell(null, $cellRowContinue);
                $table->addCell(9300, $gridSpan5)->addText('货抵', null, $cellAlignCenter);

                $table->addRow();
                $table->addCell(null, $cellRowContinue);
                $table->addCell(2000, $cellVAlignCenter)->addText('拜访时间', null, $cellAlignCenter);
                $table->addCell(2000, $cellVAlignCenter)->addText('商品名称', null, $cellAlignCenter);
                $table->addCell(1800, $cellVAlignCenter)->addText('商品单位', null, $cellAlignCenter);
                $table->addCell(3500, $gridSpan2)->addText('商品数量', null, $cellAlignCenter);
                foreach ($visit['mortgage'] as $date => $mortgages) {
                    $table->addRow();
                    $table->addCell(null, $cellRowContinue);
                    $table->addCell(2000, $cellRowSpan)->addText($date, null, $cellAlignCenter);
                    foreach ($mortgages as $mortgage) {
                        $table->addRow();
                        $table->addCell(null, $cellRowContinue);
                        $table->addCell(null, $cellRowContinue);
                        $table->addCell(2000, $cellVAlignCenter)->addText($mortgage['name'], null, $cellAlignCenter);
                        $table->addCell(1800, $cellVAlignCenter)->addText(cons()->valueLang('goods.pieces',
                            $mortgage['pieces']), null, $cellAlignCenter);
                        $table->addCell(3500, $gridSpan2)->addText($mortgage['num'], null, $cellAlignCenter);
                    }
                }
            }
            if (isset($visit['statistics'])) {
                $table->addRow();
                $table->addCell(10500, $gridSpan6)->addText('销售统计', null, $cellAlignCenter);

                $table->addRow();
                $table->addCell(1200, $cellVAlignCenter)->addText('商品ID', null, $cellAlignCenter);
                $table->addCell(2000, $cellVAlignCenter)->addText('商品名称', null, $cellAlignCenter);
                $table->addCell(2000, $cellVAlignCenter)->addText('订货数量', null, $cellAlignCenter);
                $table->addCell(1800, $cellVAlignCenter)->addText('订货总金额', null, $cellAlignCenter);
                $table->addCell(1500, $cellVAlignCenter)->addText('退货数量', null, $cellAlignCenter);
                $table->addCell(2000, $cellVAlignCenter)->addText('退货数量', null, $cellAlignCenter);
                foreach ($visit['statistics'] as $goodsId => $statistics) {
                    $table->addRow();
                    $table->addCell(1200, $cellVAlignCenter)->addText($goodsId, null, $cellAlignCenter);
                    $table->addCell(2000, $cellVAlignCenter)->addText($statistics['goods_name'], null,
                        $cellAlignCenter);
                    $table->addCell(2000, $cellVAlignCenter)->addText($statistics['order_num'], null, $cellAlignCenter);
                    $table->addCell(1800, $cellVAlignCenter)->addText($statistics['order_amount'], null,
                        $cellAlignCenter);
                    $table->addCell(1500, $cellVAlignCenter)->addText($statistics['return_order_num'], null,
                        $cellAlignCenter);
                    $table->addCell(2000, $cellVAlignCenter)->addText($statistics['return_amount'], null,
                        $cellAlignCenter);
                }
                $table->addRow();
                $table->addCell(10500,
                    $gridSpan6)->addText("订货总金额 : {$visit['amount']}   退货总金额 : {$visit['return_amount']}", null,
                    $cellAlignCenter);

            }
        }

        $name = $salesman->name . $startDate . '至' . $endDate . '业务报告明细.docx';
        $phpWord->save($name, 'Word2007', true);
    }


}
