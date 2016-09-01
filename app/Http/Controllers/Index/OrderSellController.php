<?php

namespace App\Http\Controllers\Index;


use App\Http\Requests;
use App\Models\DeliveryMan;
use App\Services\ShopService;
use Illuminate\Http\Request;
use App\Models\Order;
use PhpOffice\PhpWord\PhpWord;
use QrCode;

class OrderSellController extends OrderController
{
    /**
     * 构造方法限制终端商访问销售功能
     */
    public function __construct()
    {
        $this->middleware('retailer');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        //卖家可执行功能列表
        //支付方式
        $payType = cons()->valueLang('pay_type');
        //订单状态
        $orderStatus = cons()->lang('order.status');
        $payStatus = array_slice(cons()->lang('order.pay_status'), 0, 1, true);
        $orderStatus = array_merge($payStatus, $orderStatus);

        $search = $request->all();
        $search['search_content'] = isset($search['search_content']) ? trim($search['search_content']) : '';
        $search['pay_type'] = isset($search['pay_type']) ? $search['pay_type'] : '';
        $search['status'] = isset($search['status']) ? trim($search['status']) : '';
        $search['start_at'] = isset($search['start_at']) ? $search['start_at'] : '';
        $search['end_at'] = isset($search['end_at']) ? $search['end_at'] : '';
        $orders = Order::OfSell(auth()->id())->WithExistGoods([
            'user.shop',
            'shippingAddress.address'
        ]);
        if (is_numeric($search['search_content'])) {
            $orders = $orders->where('id', $search['search_content']);
        } elseif ($search['search_content']) {
            $orders = $orders->ofSelectOptions($search)->ofUserShopName($search['search_content']);
        } else {
            $orders = $orders->ofSelectOptions($search);
        }
        $deliveryMan = DeliveryMan::where('shop_id', auth()->user()->shop()->pluck('id'))->lists('name', 'id');

        $orders = $orders->orderBy('id', 'desc')->paginate();
        $orders->each(function ($order) {
            $order->user && $order->user->shop->setAppends([]);
        });
        return view('index.order.order-sell', [
            'pay_type' => $payType,
            'order_status' => $orderStatus,
            'data' => $this->_getOrderNum(),
            'orders' => $orders,
            'delivery_man' => $deliveryMan,
            'search' => $search
        ]);
    }

    /**
     * 待发货订单
     */
    public function getWaitSend()
    {
        $orders = Order::OfSell(auth()->id())->WithExistGoods([
            'user.shop',
        ])->NonSend();
        $deliveryMan = DeliveryMan::where('shop_id', auth()->user()->shop()->pluck('id'))->lists('name', 'id');
        return view('index.order.order-sell', [
            'data' => $this->_getOrderNum($orders->count()),
            'orders' => $orders->paginate(),
            'delivery_man' => $deliveryMan
        ]);
    }

    /**
     * 待收款订单
     */
    public function getWaitReceive()
    {
        $orders = Order::ofSell(auth()->id())->WithExistGoods(['user.shop'])->getPayment();
        return view('index.order.order-sell', [
            'data' => $this->_getOrderNum(-1, $orders->count()),
            'orders' => $orders->paginate()
        ]);
    }

    /**
     * 待确认订单
     */
    public function getWaitConfirm()
    {
        $orders = Order::ofSell(auth()->id())->WithExistGoods([
            'user.shop',
        ])->waitConfirm();
        return view('index.order.order-sell', [
            'data' => $this->_getOrderNum(-1, -1, $orders->count()),
            'orders' => $orders->paginate()
        ]);
    }


    /**
     * 查询订单详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function getDetail(Request $request)
    {
        $order = Order::OfSell(auth()->id())->with('user.shop', 'shop.user', 'goods.images.image',
            'shippingAddress.address', 'systemTradeInfo',
            'orderChangeRecode')->find(intval($request->input('order_id')));
        if (!$order) {
            return $this->error('订单不存在');
        }
        //过滤字段
        $order->goods->each(function ($goods) {
            $goods->addHidden(['introduce', 'images_url']);
        });

        $viewName = str_replace('_', '-', array_search($order->pay_type, cons('pay_type')));
        //拼接需要调用的模板名字
        $view = 'index.order.sell.detail-' . $viewName;
        $deliveryMan = DeliveryMan::where('shop_id', auth()->user()->shop()->pluck('id'))->lists('name', 'id');
        return view($view, [
            'order' => $order,
            'delivery_man' => $deliveryMan
        ]);
    }

    /**
     * 导出订单word文档,只有卖家可以导出
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function getExport(Request $request)
    {
        $orderIds = (array)$request->input('order_id');

        if (empty($orderIds)) {
            return $this->error('请选择要导出的订单', null, ['export_error' => '请选择要导出的订单']);
        }

        $status = cons('order.status');
        $result = Order::with('shippingAddress.address', 'goods')
            ->OfSell(auth()->id())->whereIn('status', [$status['non_send'], $status['send']])
            ->whereIn('id', $orderIds)->get();
        if ($result->isEmpty()) {
            return $this->error('要导出的订单不存在', null, ['export_error' => '要导出的订单不存在']);
        }
        return $this->_getExport($result);
    }

    /**
     * 生成word文档
     *
     * @param $result
     * @return string
     */
    public function _getExport($result)
    {
        // Creating the new document...
        $phpWord = new PhpWord();

        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(12);
        $phpWord->addTableStyle('table', ['borderSize' => 1], ['name' => '仿宋', 'size' => 20, 'align ' => 'center']);
        // Adding an empty Section to the document...

        $phpWord->addParagraphStyle('Normal', [
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'lineHeight' => 1.2,  // 行间距
        ]);

        $orderPayTypes = cons('pay_type');

        foreach ($result as $item) {
            $section = $phpWord->addSection();
            $table = $section->addTable('table');
            //表头
            $table->addRow(16, ['align' => 'center']);
            $table->addCell(9000, ['gridSpan' => 7])->addText('送货单');

            $table->addRow();
            $table->addCell(4300, ['gridSpan' => 3])->addText('客户:' . $item->user_shop_name);
            $table->addCell(3000, ['gridSpan' => 2])->addText('订单号:' . $item['id']);
            $table->addCell(3000, ['gridSpan' => 2])->addText('时间:' . $item->created_at->toDateString());
            $table->addRow(16);
            if ($item->pay_type == $orderPayTypes['pick_up']) {
                $table->addCell(9000, ['gridSpan' => 7])
                    ->addText('提货人：' . ($item->user->shop->contact_person) . ' ' . ($item->user->shop->contact_info));

            } else {
                $table->addCell(9000, ['gridSpan' => 7])
                    ->addText('收货地址：' . (is_null($item->shippingAddress->address) ? '' : $item->shippingAddress->address->address_name) . ' ' . ($item->shippingAddress ? $item->shippingAddress->consigner : '') . ' ' . ($item->shippingAddress ? $item->shippingAddress->phone : ''));
            }

            $table->addRow(20);
            $table->addCell(1500)->addText('货号');
            $table->addCell(1000)->addText('条形码');
            $table->addCell(1800)->addText('商品名称');
            $table->addCell(2300)->addText('促销信息');
            $table->addCell(700)->addText('数量');
            $table->addCell(1500)->addText('单价');
            $table->addCell(1500)->addText('小计');

            foreach ($item->goods as $goods) {
                $table->addRow(20);
                $table->addCell(1500)->addText($goods->id);
                $table->addCell(1500)->addText($goods->bar_code);
                $table->addCell(1800)->addText($goods->name);
                $table->addCell(3300)->addText(mb_substr($goods->promotion_info, 0, 20));
                $table->addCell(700)->addText($goods->pivot->num);
                $table->addCell(1500)->addText($goods->pivot->price . '/' . cons()->valueLang('goods.pieces',
                        $goods->pivot->pieces));
                $table->addCell(1500)->addText($goods->pivot->total_price);
            }

            $table->addRow(16);
            $table->addCell(9000, ['gridSpan' => 7])
                ->addText('合计：' . $item->price . ($item->coupon_id ? '  优惠：' . bcsub($item->price, $item->after_rebates_price, 2) . '  应付：' . $item->after_rebates_price : ''), null, ['alignMent' => 'right']);

            $table->addRow(16);
            $table->addCell(9000, ['gridSpan' => 7])->addText('付款方式：'  .$item->payment_type);

            $table->addRow();
            $cell = $table->addCell(6000, ['align' => 'left']);
            $cell->getStyle()->setGridSpan(5);
            $cell->addText('备注：'  . $item->remark);

            $qrCode = $table->addCell(3000);
            $qrCode->getStyle()->setGridSpan(2);
            $qrCode->addImage((new ShopService())->qrcode($item->shop_id),['align' => 'center']);
            $section->addFooter();
        }
        $name = date('Ymd') . strtotime('now') . '.docx';
        $phpWord->save($name, 'Word2007', true);
    }

    /**
     * 获取不同状态订单数
     *
     * @param int $nonSend
     * @param int $waitReceive
     * @param int $waitConfirm
     * @return array
     */
    private function _getOrderNum($nonSend = -1, $waitReceive = -1, $waitConfirm = -1)
    {
        $userId = auth()->id();
        $data = [
            'nonSend' => $nonSend >= 0 ? $nonSend : Order::OfSell($userId)->nonSend()->count(),
            //待发货
            'waitReceive' => $waitReceive >= 0 ? $waitReceive : Order::OfSell($userId)->getPayment()->count(),
            //待收款（针对货到付款）
            'waitConfirm' => $waitConfirm >= 0 ? $waitConfirm : Order::OfSell($userId)->waitConfirm()->count(),
            //待确认
        ];


        return $data;
    }
}
