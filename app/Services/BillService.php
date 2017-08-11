<?php
/**
 * Created by PhpStorm.
 * User: Dong
 * Date: 2017/5/23
 * Time: 14:52
 */
namespace App\Services;

use App\Models\Salesman;
use App\Models\SalesmanCustomer;
use App\Models\SalesmanVisitOrder;
use App\Models\Shop;
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
     *
     * 卖家对账
     *
     * @param $customer /客户
     * @param array $time 时间区间
     * @return array    订货单/退货单
     */
    public function seller($customer, array $time)
    {
        //客户与商家发生的订单
        $orders = $customer->orders()->where('shop_id', auth()->user()->shop_id)->get();

        $start_at = $time['start_at']->toDateTimeString();
        $end_at = $time['end_at']->toDateTimeString();
        //订货单
        $orderForm = $orders->filter(function ($item) use ($start_at, $end_at) {
            $order = $item->order;
            return $item->type == cons('salesman.order.type.order') && !empty($order) && $order->status < cons('order.status.invalid') && $order->created_at >= $start_at && $order->created_at <= $end_at && $order->pay_status < cons('order.pay_status.refund');
        });
        $orderReturn = $orders->filter(function ($item) use ($start_at, $end_at) {
            return $item->type == cons('salesman.order.type.return_order') && $item->status && $item->created_at >= $start_at && $item->created_at <= $end_at;
        });

        $finished = $orderForm->filter(function ($order) {
            $order = $order->order;
            return !is_null($order) && $order->pay_status == cons('order.pay_status.payment_success');
        });

        $finishedAmount = $finished->sum('after_rebates_price');
        //未收金额
        $notFinished = $orderForm->filter(function ($order) {
            $order = $order->order;
            return !is_null($order) && ($order->status > cons('order.status.non_confirm') && $order->pay_status < cons('order.pay_status.payment_success'));
        });
        $notFinishedAmount = $notFinished->sum('after_rebates_price');

        return compact('orderForm', 'orderReturn', 'finishedAmount', 'notFinishedAmount');
    }

    /**
     * 导出对账单
     *
     * @param $bill
     */
    public function export($bill, $store, $time)
    {

        $phpWord = new PhpWord();

        $tableBolder = array('borderSize' => 1, 'borderColor' => '999999', 'cantSplit' => true);

        $cellAlignCenter = ['align' => 'center'];
        $cellVAlignCenter = ['valign' => 'center'];
        $titleSize = ['size' => 14];
        $gridSpan2 = ['gridSpan' => 2, 'valign' => 'center'];
        $gridSpan5 = ['gridSpan' => 5, 'valign' => 'center'];
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
        $section->addText($store->name . ' — 月对账单', ['size' => '18'], ['align' => 'center']);
        if ($store instanceof Shop) {
            $section->addText('商户名称 : ' . $store->name);
            $section->addText('商户地址 : ' . $store->shopAddress->area_name);
        } else {
            $section->addText('客户名称 : ' . $store->name);
            $section->addText('客户地址 : ' . $store->business_address_name);
        }

        $section->addText('对账时间 : ' . $time['start_at']->toDateString() . ' - ' . $time['end_at']->toDateString());
        $table = $section->addTable($tableBolder);
        $table->addRow();
        $table->addCell(2250)->addText('总计金额', null, $cellAlignCenter);
        $table->addCell(2250)->addText('优惠卷金额', null, $cellAlignCenter);
        $table->addCell(2250)->addText('已支付金额', null, $cellAlignCenter);
        $table->addCell(2250)->addText('未支付金额', null, $cellAlignCenter);
        $table->addRow();
        $table->addCell(2250)->addText(sprintf("%.2f",
            $bill['orderForm']->sum('after_rebates_price')), null,
            $cellAlignCenter);
        $table->addCell(2250)->addText(sprintf("%.2f",
            $bill['orderForm']->sum('how_much_discount')-$bill['orderForm']->sum('display_fee_amount')), null, $cellAlignCenter);
        $table->addCell(2250)->addText($bill['finishedAmount'], null, $cellAlignCenter);
        $table->addCell(2250)->addText($bill['notFinishedAmount'], null, $cellAlignCenter);
        $section->addText('订单对账单', $titleSize, $cellAlignCenter);

        $table = $section->addTable($tableBolder);
        $table->addRow();
        $table->addCell(1400)->addText('时间', null, $cellAlignCenter);
        $table->addCell(1400)->addText('订单编号', null, $cellAlignCenter);
        $table->addCell(2000)->addText('商品名称', null, $cellAlignCenter);
        $table->addCell(1400)->addText('数量', null, $cellAlignCenter);
        $table->addCell(1400)->addText('金额', null, $cellAlignCenter);
        $table->addCell(1400)->addText('优惠卷', null, $cellAlignCenter);

        foreach ($bill['orderForm'] as $order) {
            if ($order->orderGoods->count()) {
                $table->addRow();
                $table->addCell(1400, $cellRowSpan)->addText($order->created_at, null, $cellAlignCenter);
                $table->addCell(1400, $cellRowSpan)->addText($order->order_id . "(" . $order->order->status_name . ")",
                    null, $cellAlignCenter);
                foreach ($order->orderGoods as $orderGoods) {
                    $table->addRow();
                    $table->addCell(1400, $cellRowContinue);
                    $table->addCell(1400, $cellRowContinue);
                    $table->addCell(2000, $cellVAlignCenter)->addText($orderGoods->goods->name, null, $cellAlignCenter);
                    $table->addCell(1400, $cellVAlignCenter)->addText($orderGoods->num . $orderGoods->pieces_name, null,
                        $cellAlignCenter);
                    $table->addCell(1400, $cellVAlignCenter)->addText($orderGoods->amount, null, $cellAlignCenter);
                    if ($order->orderGoods->first() == $orderGoods) {
                        $table->addCell(1400, $cellRowSpan)->addText(bcsub($order->how_much_discount,$order->display_fee_amount,2), null, $cellAlignCenter);
                    } else {
                        $table->addCell(1400, $cellRowContinue);
                    }
                }
            }
        }
        $section->addText('');

        $section->addText('陈列费对账单', $titleSize, $cellAlignCenter);
        $table = $section->addTable($tableBolder);
        $table->addRow();
        $table->addCell(1400)->addText('时间', null, $cellAlignCenter);
        $table->addCell(1600)->addText('订单编号', null, $cellAlignCenter);
        $table->addCell(3000)->addText('月份', null, $cellAlignCenter);
        $table->addCell(3000, $gridSpan2)->addText('陈列实发项', null, $cellAlignCenter);

        foreach ($bill['orderForm'] as $order) {
            if ($order->mortgageGoods->count()) {
                $table->addRow();
                $table->addCell(1800, $cellRowSpan)->addText($order->created_at, null, $cellAlignCenter);
                $table->addCell(1700, $cellRowSpan)->addText($order->order_id . "(" . $order->order->status_name . ")",
                    null, $cellAlignCenter);
                foreach ($order->mortgageGoods as $mortgageGoods) {
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
            } elseif ($order->displayFees->count()) {
                $table->addRow();
                $table->addCell(1800, $cellRowSpan)->addText($order->created_at, null, $cellAlignCenter);
                $table->addCell(1700, $cellRowSpan)->addText($order->order_id . "(" . $order->order->status_name . ")",
                    null, $cellAlignCenter);
                foreach ($order->displayFees as $displayFees) {
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

        $section->addText('赠品对账单', $titleSize, $cellAlignCenter);
        $table = $section->addTable($tableBolder);
        $table->addRow();
        $table->addCell(1400)->addText('时间', null, $cellAlignCenter);
        $table->addCell(1600)->addText('订单编号', null, $cellAlignCenter);
        $table->addCell(3000, $gridSpan2)->addText('赠品内容', null, $cellAlignCenter);
        foreach ($bill['orderForm'] as $order) {
            if ($order->gifts->count()) {
                $table->addRow();
                $table->addCell(1800, $cellRowSpan)->addText($order->created_at, null, $cellAlignCenter);
                $table->addCell(1700, $cellRowSpan)->addText($order->order_id . "(" . $order->order->status_name . ")",
                    null, $cellAlignCenter);
                foreach ($order->gifts as $gifts) {
                    $table->addRow();
                    $table->addCell(1800, $cellRowContinue);
                    $table->addCell(1700, $cellRowContinue);
                    $table->addCell(3700, $cellVAlignCenter)->addText($gifts->name ?? '', null,
                        $cellAlignCenter);
                    $table->addCell(1800,
                        $cellVAlignCenter)->addText($gifts->pivot->num . cons()->valueLang('goods.pieces',
                            $gifts->pivot->pieces), null,
                        $cellAlignCenter);
                }
            }
        }
        $section->addText('');

        $section->addText('促销对账单', $titleSize, $cellAlignCenter);
        $table = $section->addTable($tableBolder);
        $table->addRow();
        $table->addCell(1400)->addText('时间', null, $cellAlignCenter);
        $table->addCell(1600)->addText('订单编号', null, $cellAlignCenter);
        $table->addCell(3000)->addText('促销名称', null, $cellAlignCenter);
        $table->addCell(3000, $gridSpan2)->addText('返利内容', null, $cellAlignCenter);
        foreach ($bill['orderForm'] as $order) {
            if ($order->promo) {
                $table->addRow();
                $table->addCell(1900, $cellRowSpan)->addText($order->created_at, null, $cellAlignCenter);
                $table->addCell(1200, $cellRowSpan)->addText($order->order_id . "(" . $order->order->status_name . ")",
                    null, $cellAlignCenter);
                $table->addCell(1200, $cellRowSpan)->addText($order->promo->name, null, $cellAlignCenter);
                if (in_array($order->promo->type, [cons('promo.type.goods-goods'), cons('promo.type.money-goods')])) {
                    foreach ($order->promo->rebate as $rebate) {
                        $table->addRow();
                        $table->addCell(1900, $cellRowContinue);
                        $table->addCell(1200, $cellRowContinue);
                        $table->addCell(1200, $cellRowContinue);
                        $table->addCell(3500, $cellVAlignCenter)->addText($rebate->goods->name ?? '', null,
                            $cellAlignCenter);
                        $table->addCell(1200,
                            $cellVAlignCenter)->addText($rebate->quantity . cons()->valueLang('goods.pieces',
                                $rebate->unit), null,
                            $cellAlignCenter);
                    }
                } else {
                    $text = '(' . ($order->promo->type == cons('promo.type.custom') ? '自定义' : '现金') . ')';
                    $text .= in_array($order->promo->type, [
                        cons('promo.type.goods-money'),
                        cons('promo.type.money-money')
                    ]) ? $order->promo->rebate[0]->money . '元' : $order->promo->rebate[0]->custom;
                    $table->addCell(4700, $gridSpan2)->addText($text, null,
                        $cellAlignCenter);
                }
            }
        }
        $section->addText('');

        $section->addText('退货对账单', $titleSize, $cellAlignCenter);
        $table = $section->addTable($tableBolder);
        $table->addRow();
        $table->addCell(1800)->addText('时间', null, $cellAlignCenter);
        $table->addCell(1800)->addText('订单编号', null, $cellAlignCenter);
        $table->addCell(1800)->addText('商品名称', null, $cellAlignCenter);
        $table->addCell(1800)->addText('数量', null, $cellAlignCenter);
        $table->addCell(1800)->addText('金额', null, $cellAlignCenter);
        foreach ($bill['orderReturn'] as $order) {

            if ($order->orderGoods->count()) {
                $table->addRow();
                $table->addCell(2000, $cellRowSpan)->addText($order->created_at, null, $cellAlignCenter);
                $table->addCell(1200, $cellRowSpan)->addText($order->id, null,
                    $cellAlignCenter);
                foreach ($order->orderGoods as $orderGoods) {
                    $table->addRow();
                    $table->addCell(2000, $cellRowContinue);
                    $table->addCell(1200, $cellRowContinue);
                    $table->addCell(3500, $cellVAlignCenter)->addText($orderGoods->goods->name ?? '', null,
                        $cellAlignCenter);
                    $table->addCell(1200, $cellVAlignCenter)->addText($orderGoods->num . $orderGoods->pieces_name, null,
                        $cellAlignCenter);
                    $table->addCell(1100,
                        $cellVAlignCenter)->addText($orderGoods->amount, null,
                        $cellAlignCenter);
                }
            }
        }
        $table->addRow(0);
        $table->addCell(9000, $gridSpan5)->addText('总计金额：' . sprintf("%.2f",
                $bill['orderReturn']->sum('amount')), null, ['align' => 'right']);

        $section->addText('');

        $section->addText('');
        $section->addText('');
        $section->addText('     公司盖章:                                                    客户签字盖章:');
        $section->addText('     日期:                                                        日期:');
        $section->addText('');
        $section->addText('');
        $section->addText('如果贵方对对账单中数据有疑问，请提供贵明细，以便我们尽快核对您的账目');
        $section->addText('');

        $name = $store->name . $time['start_at']->toDateString() . ' 至 ' . $time['end_at']->toDateString() . '对账单.docx';
        return $phpWord->save(iconv('UTF-8', 'GBK//IGNORE', $name), 'Word2007', true);
    }

    /**
     * 获取用户购买过得店铺
     *
     * @param $data
     * @return mixed
     */
    public function getShop($name)
    {
        $user = auth()->user();

        $salesmanCustomer = SalesmanCustomer::where('shop_id', $user->shop_id)->select(['salesman_id'])->get();

        $salesman = Salesman::whereIn('id', $salesmanCustomer->pluck('salesman_id')->toArray())->get();

        return Shop::whereIn('id',
            $salesman->pluck($user->type == cons('user.type.supplier') ? 'maker_id' : 'shop_id'))->where(function (
            $query
        ) use ($name) {
            if ($name) {
                $query->OfName($name);
            }
        });
    }

    /**
     *
     * 买家对账
     *
     * @param $shop /对账店铺
     * @param $time /时间区间
     * @return array
     */
    public function buyer($shop, $time)
    {
        $salesmen = $shop->salesmen->pluck('id');
        //拿到作为与之对账商家的客户身份
        $customer = SalesmanCustomer::whereIn('salesman_id', $salesmen)->where('shop_id',
            auth()->user()->shop_id)->first();
        //拿到该商家名下订单
        $orders = SalesmanVisitOrder::where('salesman_customer_id', $customer->id)->where('shop_id', $shop->id)->get();

        $start_at = $time['start_at']->toDateTimeString();
        $end_at = $time['end_at']->toDateTimeString();
        //订货单
        $orderForm = $orders->filter(function ($item) use ($start_at, $end_at) {
            $order = $item->order;
            return $item->type == cons('salesman.order.type.order') && !empty($order) && $order->status < cons('order.status.invalid') && $order->created_at >= $start_at && $order->created_at <= $end_at && $order->pay_status < cons('order.pay_status.refund');
        });
        $orderReturn = $orders->filter(function ($item) use ($start_at, $end_at) {
            return $item->type == cons('salesman.order.type.return_order') && $item->status && $item->created_at >= $start_at && $item->created_at <= $end_at;
        });

        $finished = $orderForm->filter(function ($order) {
            $order = $order->order;
            return !is_null($order) && $order->pay_status == cons('order.pay_status.payment_success');
        });

        $finishedAmount = $finished->sum('after_rebates_price');
        //未收金额
        $notFinished = $orderForm->filter(function ($order) {
            $order = $order->order;
            return !is_null($order) && ($order->status > cons('order.status.non_confirm') && $order->pay_status < cons('order.pay_status.payment_success'));
        });
        $notFinishedAmount = $notFinished->sum('after_rebates_price');
        return compact('orderForm', 'orderReturn', 'finishedAmount', 'notFinishedAmount');

    }

}