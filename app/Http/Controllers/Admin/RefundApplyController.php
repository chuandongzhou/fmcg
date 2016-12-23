<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\SystemTradeInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use WeiHeng\Alipay\AlipaySubmit;

class RefundApplyController extends Controller
{
    protected $alipayPayTypes, $refundOrdersId;

    public function __construct()
    {
        $this->alipayPayTypes = [cons('trade.pay_type.alipay'), cons('trade.pay_type.alipay_pc')];
        $this->refundOrdersId = Order::where('pay_status', cons('order.pay_status.refund'))->lists('id');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        $trades = SystemTradeInfo:: whereIn('pay_type', $this->alipayPayTypes)
            ->whereIn('order_id', $this->refundOrdersId)
            ->orderBy('id', 'asc')
            ->paginate();
        return view('admin.trade.refund', ['trades' => $trades]);
    }

    /**
     * 退款
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response|\WeiHeng\Alipay\提交表单HTML文本
     */
    public function postRefund(Request $request)
    {
        $tradeIds = (array)$request->input('id');
        if (empty($tradeIds)) {
            return $this->error('请选择要退款的订单');
        }
        $trades = SystemTradeInfo::whereIn('id', $tradeIds)
            ->whereIn('pay_type', $this->alipayPayTypes)
            ->whereIn('order_id', $this->refundOrdersId)
            ->orderBy('id', 'asc')
            ->get();
        return $this->_refundHandle($trades);

    }


    /**
     * 支付宝退款处理
     *
     * @param $tradeInfo
     * @return \WeiHeng\Alipay\提交表单HTML文本
     */
    private function _refundHandle($tradeInfo)
    {
        //批次号，必填，格式：当天日期[8位]+序列号[3至24位]，如：201603081000001
        $carbon = new Carbon();
        $date = $carbon->formatLocalized('%Y%m%d');
        $batch_no = $date . $carbon->getTimestamp();

        //退款详细数据，必填，格式（支付宝交易号^退款金额^备注），多笔请用#隔开
        $detailData = $this->_getRefundData($tradeInfo);

        $alipayConf = getAlipayConfig('refund');
        $parameter = array(
            "service" => trim($alipayConf['service']),
            "partner" => trim($alipayConf['partner']),
            "notify_url" => trim($alipayConf['notify_url']),
            "seller_user_id" => trim($alipayConf['seller_id']),
            "refund_date" => trim($alipayConf['refund_date']),
            "batch_no" => $batch_no,
            "batch_num" => $tradeInfo->count(),
            "detail_data" => $detailData,
            "_input_charset" => trim(strtolower($alipayConf['input_charset']))

        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($alipayConf);
        return $alipaySubmit->buildRequestForm($parameter, "get", "确认");
    }

    /**
     * 获取退款参数
     *
     * @param $tradeInfo
     * @return string
     */
    private function _getRefundData($tradeInfo)
    {
        $data = [];
        foreach ($tradeInfo as $trade) {
            $data[] = $trade->trade_no . '^' . $trade->amount . '^' . '协商退款';
        }
        return implode('#', $data);
    }

}
