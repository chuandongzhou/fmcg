<?php

namespace App\Http\Controllers\Index;


use App\Http\Requests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\ExportWordService;
use Illuminate\Support\Facades\Redis;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Cell;

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
        $data['nonSure'] = Order::ofSell($this->userId)->nonSure()->count();//未确认
        $data['nonSend'] = Order::ofSell($this->userId)->nonSend()->count();//待发货
        $data['pendingCollection'] = Order::ofSell($this->userId)->getPayment()->count();//待收款（针对货到付款）

        $orders = Order::ofSell($this->userId)->orderBy('id', 'desc')->paginate()->toArray();

        return view('index.order.order-sell', [
            'pay_type' => $payType,
            'order_status' => $orderStatus,
            'data' => $data,
            'orders' => $orders
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

        $search = $request->input('search_content');
        $orderId = trim($search);
        if (is_numeric($orderId)) {
            $order = Order::ofSell($this->userId)->find($orderId);
            $orders['data'][0] = $order;
        } else {
            $orders = Order::ofSell($this->userId)->ofUserType($search, $this->userType)->paginate()->toArray();
        }

        return $orders;
    }

    /**
     * 处理select发送过来的请求
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getSelect(Request $request)
    {
        $search = $request->all();
        $orders = Order::ofSell($this->userId)->ofSelectOptions($search)->paginate()->toArray();

        return $orders;
    }

    /**
     * 获取待确认订单列表
     *
     * @return mixed
     */
    public function getNonSure()
    {
        $redis = Redis::connection();
        $redis->hDel('order_shop', 's' . session('shop_id'));

        return Order::ofSell($this->userId)->nonSure()->paginate()->toArray();
    }

    /**
     * 获取待发货订单列表
     *
     * @return mixed
     */
    public function getNonSend()
    {
        return Order::ofSell($this->userId)->nonSend()->paginate()->toArray();
    }

    /**
     * 获取待收款订单列表
     *
     * @return mixed
     */
    public function getPendingCollection()
    {
        return Order::ofSell($this->userId)->getPayment()->paginate()->toArray();
    }

    /**
     * 批量更新订单确认状态
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putBatchSure(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        //检查数组中未确认的订单
        $nonSure = Order::select('id', 'user_id')->where('shop_id', session('shop_id'))->where('status',
            cons('order.status.non_sure'))->whereIn('id', $orderIds)->nonCancel()->get();
        if ($nonSure->isEmpty()) {
            return $this->error('操作失败');
        }
        //写入redis的hash表
        $userIds = $nonSure->pluck('user_id');
        $redis = Redis::connection();
        foreach ($userIds as $item) {
            $redis->hMSet('order_user', 'u' . $item, 1);
        }
        //修改订单状态
        $ids = $nonSure->pluck('id');
        $status = Order::whereIn('id', $ids)->update([
            'status' => cons('order.status.non_send'),
            'confirmed_at' => Carbon::now()
        ]);
        if ($status) {
            return $this->success();
        }

        return $this->error('操作失败');
    }

    /**
     * 批量修改发货状态。不区分付款状态
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putBatchSend(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        $status = Order::ofSellByShopId($this->userId)->whereIn('id',
            $orderIds)->nonCancel()->update(['status' => cons('order.status.send'), 'send_at' => Carbon::now()]);
        if ($status) {
            return $this->success();
        }

        return $this->error('操作失败');

    }

    /**
     * 批量修改订单完成状态，无论在线支付还是货到付款都需要确认付款状态是否是付款成功
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putBatchFinish(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        $status = Order::ofSellByShopId($this->userId)->whereIn('id', $orderIds)->where('pay_status',
            cons('order.pay_status.payment_success'))->nonCancel()->update([
            'status' => cons('order.status.finished'),
            'finished_at' => Carbon::now()
        ]);
        if ($status) {
            return $this->success();
        }

        return $this->error('请确认买家是否付款');
    }

    /**
     * 查询订单详情
     *
     * @param $id
     * @return \Illuminate\View\View
     */
    public function getDetail($id)
    {
        $detail = Order::ofSellByShopId($this->userId)->with('shippingAddress', 'user', 'shop.user', 'goods',
            'shippingAddress.address')->find($id);
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
            'goods')->ofSellByShopId($this->userId)->whereIn('id', $orderIds)->get()->toArray();
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
        $phpWord->addTableStyle('tStyle', ['borderSize' => 1], ['size' => 18, 'align' => 'center']);
        // Adding an empty Section to the document...

        foreach ($res as $item) {
            $section = $phpWord->addSection();
            $table = $section->addTable('tStyle');
            //表头
            $table->addRow();
            $cell = $table->addCell(9000, ['align' => 'center', 'size' => 20]);
            $cell->getStyle()->setGridSpan(6);
            $cell->addText('送货单');
            $table->addRow();
            $cell = $table->addCell();
            $cell->getStyle()->setGridSpan(6);
            $cell->addText('订单号:' . $item['id']);

            $table->addRow(20);
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
