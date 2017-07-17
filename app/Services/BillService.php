<?php
/**
 * Created by PhpStorm.
 * User: Dong
 * Date: 2017/5/23
 * Time: 14:52
 */
namespace App\Services;

use App\Models\Shop;
use App\Models\SystemTradeInfo;
use Carbon\Carbon;
use PhpOffice\PhpWord\PhpWord;

class BillService
{
    /**
     * 对账单时间处理
     *
     * @param $time
     * @return array
     */
    public function timeHandler($time)
    {
        $start_at = (isset($time) && !empty($time)) ? new Carbon($time) : (new Carbon())->startOfMonth();
        $end_at = (isset($time) && (new Carbon($time))->format('Y-m') != Carbon::now()->format('Y-m')) ? (new Carbon($time))->endOfMonth() : Carbon::now();
        return [
            'start_at' => $start_at,
            'end_at' => $end_at,
        ];
    }

    /**
     * 获取对账单详情  卖家
     *
     * @param $customer
     * @param array $time
     * @return array
     */
    public function getBillDetailOfSeller($customer, array $time)
    {
        $start_at = array_get($time, 'start_at')->toDateTimeString();
        $end_at = array_get($time, 'end_at')->toDateTimeString();

        //获取时间范围内的业务订单
        $businessOrders = $customer->orders()->with('orderGoods', 'displayFees', 'mortgageGoods')->where('created_at',
            '>=', $start_at)->where('created_at', '<=', $end_at)->get();
        //判断客户是否有线上店铺
        if ($customer->shop_id) {
            //全部订单
            $allOrders = auth()->user()->shop->orders()->Useful()->NoInvalid()->NonRefund()->where('user_id',
                $customer->shop_id)->where('created_at', '>=',
                $start_at)->where('created_at', '<=', $end_at)->get();
        } else {
            $allOrders = $customer->orders()->with('orderGoods')->where('created_at',
                '>=', $start_at)->where('created_at', '<=', $end_at)->get();
           // dd($allOrders);
        }

        //收支明细
        $paymentDetails = $this->getPaymentDetails($allOrders->pluck('id'), $start_at, $end_at);

        return compact('businessOrders', 'allOrders', 'paymentDetails', 'time', 'customer');
    }

    /**
     * 导出对账单  卖家
     *
     * @param $bill
     */
    public function billExportOfSeller($bill)
    {
        $phpWord = new PhpWord();

        $tableBolder = array('borderSize' => 1, 'borderColor' => '999999', 'cantSplit' => true);

        $cellAlignCenter = ['align' => 'center'];
        $cellVAlignCenter = ['valign' => 'center'];
        $titleSize = ['size' => 14];
        $gridSpan2 = ['gridSpan' => 2, 'valign' => 'center'];
        $gridSpan6 = ['gridSpan' => 6, 'valign' => 'center'];
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
        if (isset($bill['customer'])) {
            $section->addText($bill['customer']->name . ' — 月对账单', ['size' => '18'], ['align' => 'center']);
            $section->addText('客户名称 : ' . $bill['customer']->name);
            $section->addText('客户地址 : ' . $bill['customer']->business_address_name);
        } else {
            $section->addText($bill['shop']->name . ' — 月对账单', ['size' => '18'], ['align' => 'center']);
            $section->addText('商户名称 : ' . $bill['shop']->name);
            $section->addText('商户地址 : ' . $bill['shop']->shopAddress->area_name);
        }

        $section->addText('对账时间 : ' . $bill['time']['start_at']->toDateString() . ' - ' . $bill['time']['end_at']->toDateString());

        $section->addText('陈列费对账单', $titleSize, $cellAlignCenter);
        $table = $section->addTable($tableBolder);
        $table->addRow();
        $table->addCell(1400)->addText('时间', null, $cellAlignCenter);
        $table->addCell(1600)->addText('订单编号', null, $cellAlignCenter);
        $table->addCell(3000)->addText('月份', null, $cellAlignCenter);
        $table->addCell(3000, $gridSpan2)->addText('陈列实发项', null, $cellAlignCenter);

        foreach ($bill['businessOrders'] as $businessOrder) {
            if ($businessOrder->mortgageGoods->count()) {
                $table->addRow();
                $table->addCell(1800, $cellRowSpan)->addText($businessOrder->created_at, null, $cellAlignCenter);
                $table->addCell(1700, $cellRowSpan)->addText($businessOrder->id, null, $cellAlignCenter);
                foreach ($businessOrder->mortgageGoods as $mortgageGoods) {
                    $table->addRow();
                    $table->addCell(1800, $cellRowContinue);
                    $table->addCell(1700, $cellRowContinue);

                    $table->addCell(1700, $cellVAlignCenter)->addText($mortgageGoods->pivot->month ?? '', null,
                        $cellAlignCenter);
                    $table->addCell(2500, $cellVAlignCenter)->addText($mortgageGoods->goods_name, null,
                        $cellAlignCenter);
                    $table->addCell(1300,
                        $cellVAlignCenter)->addText(intval($mortgageGoods->pivot->used) . $mortgageGoods->pieces_name,
                        null, $cellAlignCenter);
                }
            } elseif ($businessOrder->displayFees->count()) {
                $table->addRow();
                $table->addCell(1800, $cellRowSpan)->addText($businessOrder->created_at, null, $cellAlignCenter);
                $table->addCell(1700, $cellRowSpan)->addText($businessOrder->id, null, $cellAlignCenter);
                foreach ($businessOrder->displayFees as $displayFees) {
                    $table->addRow();
                    $table->addCell(1800, $cellRowContinue);
                    $table->addCell(1700, $cellRowContinue);

                    $table->addCell(1700, $cellVAlignCenter)->addText($displayFees->month, null,
                        $cellAlignCenter);
                    $table->addCell(2500, $cellVAlignCenter)->addText('现金', null,
                        $cellAlignCenter);
                    $table->addCell(1300, $cellVAlignCenter)->addText('￥' . $displayFees->used, null, $cellAlignCenter);
                }
            }
        }
        $section->addText('');

        $section->addText('订单对账单', $titleSize, $cellAlignCenter);
        $table = $section->addTable($tableBolder);
        $table->addRow();
        $table->addCell(1400)->addText('时间', null, $cellAlignCenter);
        $table->addCell(1400)->addText('订单编号', null, $cellAlignCenter);
        $table->addCell(2000)->addText('商品名称', null, $cellAlignCenter);
        $table->addCell(1400)->addText('数量', null, $cellAlignCenter);
        $table->addCell(1400)->addText('金额', null, $cellAlignCenter);
        $table->addCell(1400)->addText('优惠卷', null, $cellAlignCenter);

        foreach ($bill['allOrders'] as $order) {
            if ($order->orderGoods->count()) {
                $table->addRow();
                $table->addCell(1400, $cellRowSpan)->addText($order->created_at, null, $cellAlignCenter);
                $table->addCell(1400, $cellRowSpan)->addText($order->id, null, $cellAlignCenter);
                foreach ($order->orderGoods as $orderGoods) {
                    $table->addRow();
                    $table->addCell(1400, $cellRowContinue);
                    $table->addCell(1400, $cellRowContinue);
                    $table->addCell(2000, $cellVAlignCenter)->addText($orderGoods->goods->name, null, $cellAlignCenter);
                    $table->addCell(1400, $cellVAlignCenter)->addText($orderGoods->num, null, $cellAlignCenter);
                    $table->addCell(1400, $cellVAlignCenter)->addText($orderGoods->total_price, null, $cellAlignCenter);
                    if ($order->orderGoods->first() == $orderGoods) {
                        $table->addCell(1400, $cellRowSpan)->addText($order->how_much_discount, null, $cellAlignCenter);
                    } else {
                        $table->addCell(1400, $cellRowContinue);
                    }
                }
            }
        }
        $table->addRow(0);
        $table->addCell(9000, $gridSpan6)->addText('总计金额：' . sprintf("%.2f",
                $bill['allOrders']->sum('price')) . ' 优惠总额：' . sprintf("%.2f",
                $bill['allOrders']->sum('how_much_discount')), null, ['align' => 'right']);

        $section->addText('');
        $section->addText('收支明细', $titleSize, $cellAlignCenter);
        $table = $section->addTable($tableBolder);
        $table->addRow();
        $table->addCell(2200)->addText('时间', null, $cellAlignCenter);
        $table->addCell(1700)->addText('支付平台', null, $cellAlignCenter);
        $table->addCell(1700)->addText('交易号', null, $cellAlignCenter);
        $table->addCell(1700)->addText('手续费', null, $cellAlignCenter);
        $table->addCell(1700)->addText('收支金额', null, $cellAlignCenter);

        foreach ($bill['paymentDetails'] as $paymentDetail) {
            $table->addRow(0);
            $table->addCell(2200, $cellVAlignCenter)->addText($paymentDetail->finished_at, null, $cellAlignCenter);
            $table->addCell(1700, $cellVAlignCenter)->addText(cons()->valueLang('trade.pay_type', $paymentDetail->type),
                null, $cellAlignCenter);
            $table->addCell(1700, $cellVAlignCenter)->addText($paymentDetail->trade_no, null, $cellAlignCenter);
            $table->addCell(1700, $cellVAlignCenter)->addText($paymentDetail->target_fee ?? '0', null,
                $cellAlignCenter);
            $table->addCell(1700, $cellVAlignCenter)->addText('¥' . $paymentDetail->amount, null, $cellAlignCenter);
        }
        $table->addRow(0);
        $table->addCell(9000, $gridSpan6)->addText('总计收支金额：' . number_format($bill['paymentDetails']->sum('amount'), 2),
            null, ['align' => 'right']);
        $section->addText('');
        $section->addText('');
        $section->addText('     公司盖章:                                                    客户签字盖章:');
        $section->addText('     日期:                                                        日期:');
        $section->addText('');
        $section->addText('');
        $section->addText('如果贵方对对账单中数据有疑问，请提供贵明细，以便我们尽快核对您的账目');
        $section->addText('');
        $name = (isset($bill['customer']) ? $bill['customer']->name : $bill['shop']->name) . $bill['time']['start_at']->toDateString() . ' 至 ' . $bill['time']['end_at']->toDateString() . '对账单.docx';
        return $phpWord->save(iconv('UTF-8', 'GBK//IGNORE', $name), 'Word2007', true);
    }

    /**
     * 获取用户购买过得店铺
     *
     * @param $data
     * @return mixed
     */
    public function getBillListsOfBuyer($name)
    {
        $orders = auth()->user()->orders()->groupBy('shop_id')->select(['shop_id'])->get();
        return Shop::whereIn('id', $orders->pluck('shop_id'))->where(function ($query) use ($name) {
            if ($name) {
                $query->OfName($name);
            }
        });
    }

    public function getBillDetailOfBuyer($shop, $time)
    {
        $start_at = array_get($time, 'start_at')->toDateTimeString();
        $end_at = array_get($time, 'end_at')->toDateTimeString();

        $allOrders = auth()->user()->orders()->Useful()->NoInvalid()->NonRefund()->where('shop_id',
            $shop->id)->where('created_at', '>=',
            $start_at)->where('created_at', '<=', $end_at)->get();
        $businessOrders = $allOrders->pluck('salesmanVisitOrder')->filter(function ($item) {
            return !is_null($item);
        });
        $paymentDetails = $this->getPaymentDetails($allOrders->pluck('id'), $start_at, $end_at);

        return compact('allOrders', 'businessOrders', 'paymentDetails', 'time', 'shop');

    }


    public function getPaymentDetails($ordersId, $start_at, $end_at)
    {
        return SystemTradeInfo::select('trade_no', 'pay_type', 'type', 'amount', 'target_fee',
            'finished_at')->where('success_at', '>=',
            $start_at)->where('success_at', '<=', $end_at)->where('is_finished',
            cons('trade.is_finished.yes'))->whereIn('order_id', $ordersId)->orderBy('finished_at')->get();
    }
}