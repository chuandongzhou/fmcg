<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/28
 * Time: 10:05
 */
namespace App\Http\Controllers\Index;

use App\Models\Order;
use Illuminate\Http\Request;
use DB;
use Gate;

class YeePayController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param $orderId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getRequest(Request $request, $orderId)
    {
        $type = $request->input('type');
        $field = $type == 'all' ? 'pid' : 'id';
        $orders = Order::where($field, $orderId)->get();

        if (Gate::denies('validate-online-orders', $orders)) {
            return redirect('order-buy');
        }

//	商家设置用户购买商品的支付信息.
//易宝支付平台统一使用GBK/GB2312编码方式,参数如用到中文，请注意转码

//	商户订单号,选填.
//若不为""，提交的订单号必须在自身账户交易中唯一;为""时，易宝支付会自动生成随机的商户订单号.
        $p2_Order = $orderId;

//	支付金额,必填.
//单位:元，精确到分.
        $p3_Amt = 0.01;//$orders->pluck('price')->sum();

//	交易币种,固定值"CNY".
        $p4_Cur = config('yeepay.p4_cur');

//	商品名称
//用于支付时显示在易宝支付网关左侧的订单产品信息.
        $p5_Pid = iconv('UTF-8', 'GB2312', '商品名');
//	商品种类
        $p6_Pcat = 'fmcg_type';

//	商品描述
        $p7_Pdesc = 'fmcg_describe';

//	商户接收支付成功数据的地址,支付成功后易宝支付会向该地址发送两次成功通知.
        $p8_Url = url('webhooks/yeepay/success');//config('yeepay.p8_url');

//	商户扩展信息
//商户可以任意填写1K 的字符串,支付成功时将原样返回.
        //商家账号
        $pa_MP = $type;

//	支付通道编码
//默认为""，到易宝支付网关.若不需显示易宝支付的页面，直接跳转到各银行、神州行支付、骏网一卡通等支付页面，该字段可依照附录:银行列表设置参数值.
        $pd_FrpId = '';

//	应答机制
//默认为"1": 需要应答机制;
        $pr_NeedResponse = config('yeepay.pr_need_response');

//调用签名函数生成签名串
        $hmac = getReqHmacString($p2_Order, $p3_Amt, $p4_Cur, $p5_Pid, $p6_Pcat, $p7_Pdesc, $p8_Url, $pa_MP, $pd_FrpId,
            $pr_NeedResponse);

        $postData = [
            'p0_Cmd' => config('yeepay.p0_cmd'),
            'p1_MerId' => config('yeepay.p1_mer_id'),
            'p2_Order' => $p2_Order,
            'p3_Amt' => $p3_Amt,
            'p4_Cur' => config('yeepay.p4_cur'),
            "p5_Pid" => $p5_Pid,
            'p6_Pcat' => $p6_Pcat,
            'p7_Pdesc' => $p7_Pdesc,
            'p8_Url' => $p8_Url,
            'p9_SAF' => config('yeepay.p9_saf'),
            'pa_MP' => $pa_MP,
            'pd_FrpId' => $pd_FrpId,
            'pr_NeedResponse' => $pr_NeedResponse,
            'hmac' => $hmac
        ];

        $postUrl = config('yeepay.req_url_online');

        $url = $postUrl . '?' . http_build_query($postData);
        return redirect($url);
    }
}