<?php

namespace App\Http\Controllers\Index\Business;

use App\Http\Controllers\Index\Controller;
use Gate;
use App\Models\SalesmanVisitOrder;
use App\Services\BusinessService;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;

class SalesmanVisitOrderController extends Controller
{

    /**
     * 订货单
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function orderForms(Request $request)
    {
        $shop = auth()->user()->shop;
        $salesmen = $shop->salesmen;
        $salesmenId = $salesmen->pluck('id');
        $data = $request->all();
        $filter = array_merge($data, ['type' => cons('salesman.order.type.order')]);
        $orders = (new BusinessService())->getOrders($salesmenId, $filter, ['salesmanCustomer', 'salesman', 'order']);

        return view('index.business.order-order-forms',
            ['orders' => $orders, 'salesmen' => $salesmen, 'data' => $data]);
    }

    /**
     * 退货单
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function returnOrders(Request $request)
    {
        $shop = auth()->user()->shop;

        $salesmen = $shop->salesmen;

        $salesmenId = $salesmen->pluck('id');
        $data = $request->all();
        $filter = array_merge($data, ['type' => cons('salesman.order.type.return_order')]);

        $orders = (new BusinessService())->getOrders($salesmenId, $filter);
        return view('index.business.order-return-orders',
            ['orders' => $orders, 'salesmen' => $salesmen, 'data' => $data]);
    }

    /**
     * 退货单打印
     *
     * @param \App\Models\SalesmanVisitOrder $salesmanVisitOrder
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function browserExport(SalesmanVisitOrder $salesmanVisitOrder)
    {
        if (Gate::denies('validate-salesman-order', $salesmanVisitOrder)) {
            return $this->error('订单不存在');
        }

        return view('index.business.order-print', (new BusinessService)->getOrderData($salesmanVisitOrder));
    }

    /**
     * 订单详情
     *
     * @param \App\Models\SalesmanVisitOrder $salesmanVisitOrder
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(SalesmanVisitOrder $salesmanVisitOrder)
    {
        if (Gate::denies('validate-salesman-order',
                $salesmanVisitOrder) || (!$salesmanVisitOrder->can_pass) && $salesmanVisitOrder->type == cons('salesman.order.type.order')
        ) {
            return $this->error('订单不存在');
        }
        $isOrderForm = $salesmanVisitOrder->type == cons('salesman.order.type.order');
        if ($isOrderForm) {
            $viewName = 'order-detail';
            $load = [
                'mortgageGoods.goods',
                'applyPromo',
                'orderGoods.goods',
                'gifts.goodsPieces' => function ($query) {
                    $query->select('goods_id', 'pieces_level_1', 'pieces_level_2', 'pieces_level_3');
                }
            ];
        } else {
            $viewName = 'order-return-detail';
            $load = ['mortgageGoods.goods', 'orderGoods.goods'];
        }
        // $viewName = $isOrderForm ? 'order-return-detail' : 'order-detail';
        return view('index.business.' . $viewName, (new BusinessService)->getOrderData($salesmanVisitOrder, $load));
    }

    /**
     * 订单导出
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function export(Request $request)
    {
        $orderIds = $request->input('order_id');

        if (empty($orderIds)) {
            return $this->error('请选择要导出的订单');
        }
        $orders = SalesmanVisitOrder::whereIn('id', $orderIds)->get();
        if (Gate::denies('validate-salesman-order', $orders)) {
            return $this->error('存在不合法订单');
        }
        // Creating the new document...
        $phpWord = new PhpWord();

        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');

        $cellAlignRight = array('align' => 'right');
        $cellAlignCenter = array('align' => 'center');
        $gridSpan7 = ['gridSpan' => 7];
        $gridSpan2 = ['gridSpan' => 2];
        $gridSpan3 = ['gridSpan' => 3];
        $gridSpan4 = ['gridSpan' => 4];
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');


        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(12);
        $phpWord->addTableStyle('table', $styleTable);

        $phpWord->addParagraphStyle('Normal', [
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'lineHeight' => 1.2,  // 行间距
        ]);

        foreach ($orders as $order) {
            if (!$order->can_export) {
                return $this->error('存在不可导出订单订单');
            }

            $data = (new BusinessService())->getOrderData($order);


            $section = $phpWord->addSection();
            $table = $section->addTable('table');


            //第一行
            $table->addRow(16);
            $table->addCell(4500, $gridSpan3)->addText('单号 :' . $order->id);
            $table->addCell(4500, $gridSpan4)->addText($order->created_at->toDateString(), null, $cellAlignRight);

            //第二行
            $table->addRow(16);
            $table->addCell(4500, $gridSpan3)->addText('客户名称 :' . $order->customer_name);
            $table->addCell(4500, $gridSpan4)->addText('业务员 :' . $order->salesman_name, null, $cellAlignRight);

            $table->addRow(16);
            $table->addCell(9000, $gridSpan7)->addText('联系人 :' . $order->customer_contact);

            $table->addRow(16);
            $table->addCell(9000, $gridSpan7)->addText('收货地址 :' . $order->shipping_address);

            if ($order->type == cons('salesman.order.type.order')) {
                $table->addRow(16);
                $table->addCell(9000, $gridSpan7)->addText('订单备注 :' . $order->order_remark);

                $table->addRow(16);
                $table->addCell(9000, $gridSpan7)->addText('陈列费备注 :' . $order->display_remark);
            }

            $table->addRow(16);
            $table->addCell(1300, $cellRowSpan)->addText('订货商品', null, $cellAlignCenter);
            $table->addCell(1300)->addText('平台商品ID', null, $cellAlignCenter);
            $table->addCell(1300)->addText('商品名称', null, $cellAlignCenter);
            $table->addCell(1300)->addText('商品单价', null, $cellAlignCenter);
            $table->addCell(1300)->addText('订货数量', null, $cellAlignCenter);
            $table->addCell(1300)->addText('合计', null, $cellAlignCenter);
            $table->addCell(1300, $cellRowSpan)->addText('总计: ' . $data['orderGoods']->sum('amount'), null,
                $cellAlignCenter);
            foreach ($data['orderGoods'] as $orderGood) {
                $table->addRow(16);
                $table->addCell(null, $cellRowContinue);
                $table->addCell(1300)->addText($orderGood->goods_id, null, $cellAlignCenter);
                $table->addCell(1300)->addText(str_limit($orderGood->goods_name, 10), null, $cellAlignCenter);
                $table->addCell(1300)->addText($orderGood->price . '/' . cons()->valueLang('goods.pieces',
                        $orderGood->pieces), null, $cellAlignCenter);
                $table->addCell(1300)->addText($orderGood->num, null, $cellAlignCenter);
                $table->addCell(1300)->addText($orderGood->amount, null, $cellAlignCenter);
                $table->addCell(null, $cellRowContinue);
            }
            if ($order->type == cons('salesman.order.type.order')) {
                if($order->display_fee_amount){
                    $table->addRow(16);
                    $table->addCell(9000, $gridSpan7)->addText('陈列费 :');

                    $table->addRow(16);
                    $table->addCell(9000, $gridSpan7)->addText('现金 :' . sprintf('%.2f',$order->display_fee_amount));
                }

                if (!$data['mortgageGoods']->isEmpty()) {
                    $table->addRow(16);
                    $table->addCell(1300, $cellRowSpan)->addText('抵费商品', null, $cellAlignCenter);
                    $table->addCell(1300, $gridSpan4)->addText('商品名称', null, $cellAlignCenter);
                    $table->addCell(1300)->addText('商品单位', null, $cellAlignCenter);
                    $table->addCell(1300)->addText('数量', null, $cellAlignCenter);

                    foreach ($data['mortgageGoods'] as $mortgageGoods) {
                        $table->addRow(16);
                        $table->addCell(null, $cellRowContinue);
                        $table->addCell(1300, $gridSpan4)->addText($mortgageGoods['name'], null, $cellAlignCenter);
                        $table->addCell(1300)->addText(cons()->valueLang('goods.pieces',
                            $mortgageGoods['pieces']), null, $cellAlignCenter);
                        $table->addCell(1300)->addText($mortgageGoods['num'], null, $cellAlignCenter);
                    }

                }
            }

            $table->addRow(16);
            $table->addCell(1300, $cellRowSpan)->addText('赠品', null, $cellAlignCenter);
            $table->addCell(1300, $gridSpan4)->addText('名称', null, $cellAlignCenter);
            $table->addCell(1300)->addText('单位', null, $cellAlignCenter);
            $table->addCell(1300)->addText('数量', null, $cellAlignCenter);

            foreach ($order->gifts as $gift) {
                $table->addRow(16);
                $table->addCell(null, $cellRowContinue);
                $table->addCell(1300, $gridSpan4)->addText($gift->name, null, $cellAlignCenter);
                $table->addCell(1300)->addText(cons()->valueLang('goods.pieces',
                    $gift->pivot->pieces), null, $cellAlignCenter);
                $table->addCell(1300)->addText($gift->pivot->num, null, $cellAlignCenter);
            }

            if ($order->promo) {
                $table->addRow(16);
                $table->addCell(1300, $cellRowSpan)->addText('促销活动', null, $cellAlignCenter);
                $table->addCell(1300, $gridSpan4)->addText('促销名称', null, $cellAlignCenter);
                $table->addCell(3000, $gridSpan2)->addText('返利内容', null, $cellAlignCenter);
                if (in_array($order->promo->type, [cons('promo.type.goods-goods'), cons('promo.type.money-goods')])) {
                    foreach ($order->promo->rebate as $rebate) {
                        $table->addRow();
                        $table->addCell(null, $cellRowContinue);
                        if ($rebate == $order->promo->rebate->first()) {
                            $table->addCell(3500,
                                array_merge($cellRowSpan, $gridSpan4))->addText($order->promo->name ?? '', null,
                                $cellAlignCenter);
                        } else {
                            $table->addCell(null, array_merge($cellRowContinue, $gridSpan4));
                        }
                        $table->addCell(3500, $cellAlignCenter)->addText($rebate->goods->name ?? '', null,
                            $cellAlignCenter);
                        $table->addCell(3500,
                            $cellAlignCenter)->addText($rebate->quantity . cons()->valueLang('goods.pieces',
                                $rebate->unit), null,
                            $cellAlignCenter);
                    }
                } else {
                    $table->addRow();
                    $table->addCell(null, $cellRowContinue);
                    $table->addCell(3500, array_merge($cellAlignCenter, $gridSpan4))->addText($order->promo->name ?? '',
                        null,
                        $cellAlignCenter);
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
        $name = date('Ymd') . strtotime('now') . '.docx';
        $phpWord->save($name, 'Word2007', true);

    }
}
