<?php

namespace App\Http\Controllers\Index;


use App\Http\Requests;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use App\Models\Order;
use PhpOffice\PhpWord\PhpWord;

class OrderSellController extends OrderController
{
    /**
     * 构造方法限制终端商访问销售功能
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('retailer');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        //卖家可执行功能列表
        //支付方式
        $payType = cons()->valueLang('pay_type');

        //订单状态
        $orderStatus = cons()->lang('order.status');
        $payStatus = array_slice(cons()->lang('order.pay_status'), 0, 1, true);
        $orderStatus = array_merge($payStatus, $orderStatus);
        $data['nonSend'] = Order::bySellerId($this->userId)->nonSend()->count();//待发货
        $data['pendingCollection'] = Order::bySellerId($this->userId)->getPayment()->count();//待收款（针对货到付款）
        //卖家订单首页不显示在线支付未付款订单
        $orders = Order::bySellerId($this->userId)->exceptNonPayment()->with('user.shop', 'goods')->orderBy('id',
            'desc')->paginate()->toArray();
        $deliveryMan = DeliveryMan::where('shop_id', auth()->user()->shop->id)->lists('name', 'id');

        return view('index.order.order-sell', [
            'pay_type' => $payType,
            'order_status' => $orderStatus,
            'data' => $data,
            'orders' => $orders,
            'delivery_man' => $deliveryMan
        ]);
    }

    /**
     * 处理搜索按钮发送过来的请求
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getSearch(Request $request)
    {
        $search = $request->all();
        $orderId = trim($search['search_content']);
        if (is_numeric($orderId)) {
            $order = Order::ofSell($this->userId)->ofSelectOptions($search)->find($orderId);
            $orders['data'][0] = $order;
        } else {
            $orders = Order::ofSell($this->userId)->ofSelectOptions($search)->ofUserShopName($orderId)->paginate()->toArray();
        }

        return $orders;
    }

    /**
     * 查询订单详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function getDetail(Request $request)
    {
        $detail = Order::bySellerId($this->userId)->with('shippingAddress', 'user', 'shop.user', 'goods',
            'shippingAddress.address')->find(intval($request->input('order_id')));
        if (!$detail) {
            return $this->error('订单不存在');
        }
        $detail = $detail->toArray();
        //拼接需要调用的模板名字
        $folderName = array_flip(cons('user.type'))[$this->userType];
        $payType = $detail['pay_type'];
        $fileName = array_flip(cons('pay_type'))[$payType];

        $view = 'index.order.' . $folderName . '.detail-' . $fileName;

        return view($view, [
            'order' => $detail
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
        $res = Order::with('shippingAddress', 'shippingAddress.address',
            'goods')->bySellerId($this->userId)->where('status', cons('order.status.send'))->whereIn('id',
            $orderIds)->get()->toArray();
        if (empty($res)) {
            return $this->error('没有该订单信息');
        }
        $this->_getContent($res);
    }

    /**
     * 生成word文档
     *
     * @param $res
     * @return string
     */
    public function _getContent($res)
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

        foreach ($res as $item) {
            $section = $phpWord->addSection();
            $table = $section->addTable('table');
            //表头
            $table->addRow(16, ['align' => 'center']);
            $cell = $table->addCell(9000);
            $cell->getStyle()->setGridSpan(6);
            $cell->addText('送货单');
            $table->addRow();
            $cell = $table->addCell();
            $cell->getStyle()->setGridSpan(6);
            $cell->addText('订单号:' . $item['id']);

            $table->addRow(20, ['align' => 'center']);
            $table->addCell(1500)->addText('货号');
            $table->addCell(1800)->addText('商品名称');
            $table->addCell(3300)->addText('促销信息');
            $table->addCell(700)->addText('数量');
            $table->addCell(1500)->addText('单价');
            $table->addCell(1500)->addText('小计');
            foreach ($item['goods'] as $good) {
                $table->addRow(20);
                $table->addCell(1500)->addText($good['id']);
                $table->addCell(1800)->addText($good['name']);
                $table->addCell(3300)->addText(mb_substr($good['promotion_info'], 0, 20));
                $table->addCell(700)->addText($good['pivot']['num']);
                $table->addCell(1500)->addText($good['pivot']['price']);
                $table->addCell(1500)->addText($good['pivot']['total_price']);
            }
            $info = [
                '付款方式:   ' . $item['payment_type'],
                '备注:   ' . $item['remark'],
                '合计:   ' . $item['price'],
                '收货人:   ' . $item['shipping_address']['consigner'],
                '电话:   ' . $item['shipping_address']['phone'],
                '地址:   ' . $item['shipping_address']['address']['address']
            ];
            foreach ($info as $v) {
                $table->addRow();
                $cell = $table->addCell();
                $cell->getStyle()->setGridSpan(6);
                $cell->addText($v);
            }
            $table->addRow();
            $cell = $table->addCell(9000, ['align' => 'left']);
            $cell->getStyle()->setGridSpan(6);
            $cell->addText(date('Y-m-d'));
            $section->addFooter();
        }
        $name = date('Ymd') . strtotime('now') . '.docx';
        $phpWord->save($name, 'Word2007', true);
    }
}
