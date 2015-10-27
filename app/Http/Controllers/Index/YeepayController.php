<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/28
 * Time: 10:05
 */
namespace App\Http\Controllers\Index;

use App\Models\Order;
use App\Models\Shop;
use App\Models\SystemTradeInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

class YeePayController extends Controller
{
    /**
     * @param $orderId
     * @return \Illuminate\View\View
     */
    public function getRequest($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return $this->error('订单不存在');
        }
        if ($order->pay_type != cons('pay_type.online')) {
            return $this->error('此订单不是在线支付订单');
        }
        $orderConfig = cons('order');

        if ($order->pay_status != $orderConfig['pay_status']['non_payment']) {
            return $this->error('此订单已付款');
        }


#	商家设置用户购买商品的支付信息.
##易宝支付平台统一使用GBK/GB2312编码方式,参数如用到中文，请注意转码

#	商户订单号,选填.
##若不为""，提交的订单号必须在自身账户交易中唯一;为""时，易宝支付会自动生成随机的商户订单号.
        $p2_Order = $orderId;

#	支付金额,必填.
##单位:元，精确到分.
        $p3_Amt = 0.01;//$order->price;

#	交易币种,固定值"CNY".
        $p4_Cur = config('yeepay.p4_cur');

#	商品名称
##用于支付时显示在易宝支付网关左侧的订单产品信息.
        $p5_Pid = iconv('UTF-8', 'GB2312', '商品名');
#	商品种类
        $p6_Pcat = 'fmcg_type';

#	商品描述
        $p7_Pdesc = 'fmcg_describe';

#	商户接收支付成功数据的地址,支付成功后易宝支付会向该地址发送两次成功通知.
        $p8_Url = config('yeepay.p8_url');

#	商户扩展信息
##商户可以任意填写1K 的字符串,支付成功时将原样返回.
        //商家账号
        $pa_MP = Shop::find($order->shop_id)->user->user_name;

#	支付通道编码
##默认为""，到易宝支付网关.若不需显示易宝支付的页面，直接跳转到各银行、神州行支付、骏网一卡通等支付页面，该字段可依照附录:银行列表设置参数值.
        $pd_FrpId = '';

//	应答机制
##默认为"1": 需要应答机制;
        $pr_NeedResponse = config('yeepay.pr_need_response');

#调用签名函数生成签名串
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

        $postUrl = config('yeepay.req_url_onLine');

        $url = $postUrl . '?' . http_build_query($postData);
        return redirect($url);
    }


    public function anyCallback(Request $request)
    {
#	只有支付成功时易宝支付才会通知商户.
##支付成功回调有两次，都会通知到在线支付请求参数中的p8_Url上：浏览器重定向;服务器点对点通讯.

#	解析返回参数.
        $r0_Cmd = $request->input('r0_Cmd');                //固定值：Buy
        $r1_Code = $request->input('r1_Code');              //固定值：1 - 代表支付成功
        $r2_TrxId = $request->input('r2_TrxId');            //易宝交易流水号
        $r3_Amt = $request->input('r3_Amt');                //支付金额
        $r4_Cur = $request->input('r4_Cur');                //交易币种（RMB）
        $r5_Pid = $request->input('r5_Pid');                //商品名
        $r6_Order = $request->input('r6_Order');            //商户订单号
        $r7_Uid = $request->input('r7_Uid');                //易宝支付会员 ID
        $r8_MP = $request->input('r8_MP');                  //商户扩展信息
        $r9_BType = $request->input('r9_BType');            //通知类型
        $rq_TargetFee = $request->input('rq_TargetFee');    //商户手续费
        $hmac = $request->input('hmac');                    //hmac

#	判断返回签名是否正确（True/False）
        $bRet = CheckHmac($r0_Cmd, $r1_Code, $r2_TrxId, $r3_Amt, $r4_Cur, $r5_Pid, $r6_Order, $r7_Uid, $r8_MP,
            $r9_BType, $hmac);
#	以上代码和变量不需要修改.


#	校验码正确.
        if ($bRet) {
            if ($r1_Code == "1") {

                #	需要比较返回的金额与商家数据库中订单的金额是否相等，只有相等的情况下才认为是交易成功.
                #	并且需要对返回的处理进行事务控制，进行记录的排它性处理，在接收到支付结果通知后，判断是否进行过业务逻辑处理，不要重复进行业务逻辑处理，防止对同一条交易重复发货的情况发生.

                if ($r9_BType == "1") {
                    //TODO:跳转至交易成功
                    return redirect(url('order-buy'));
                } elseif ($r9_BType == "2") {
                    //如果需要应答机制则必须回写流,以success开头,大小写不敏感.;

                    //更改订单状态
                    $orderConf = cons('order');
                    $order = Order::find($r6_Order);
                    $order->fill([
                        'pay_status' => $orderConf['pay_status']['payment_success'],
                    ])->save();
                    // 增加易宝支付log
                    DB::table('yeepay_log')->insert(
                        [
                            'order_id' => $r6_Order,
                            'trade_no' => $r2_TrxId,
                            'amount' => $r3_Amt,
                        ]
                    );
                    //增加系统交易信息
                    $tradeConf = cons('trade');
                    SystemTradeInfo::create([
                        'type' => $tradeConf['type']['in'],
                        'pay_type' => $tradeConf['pay_type']['yeepay'],
                        'account' => $r8_MP,
                        'paid_at' => Carbon::now(),
                        'order_id' => $r6_Order,
                        'trade_no' => $r2_TrxId,
                        'pay_status' => $tradeConf['pay_status']['success'],
                        'amount' => $r3_Amt,
                        'target_fee' => $rq_TargetFee,
                        'trade_currency' => $tradeConf['trade_currency']['rmb'],
                        'callback_type' => 'json',
                        'hmac' => $hmac,
                        'success_at' => Carbon::now(),
                    ]);
                    //TODO: 增加商家余额
                    die ("success");
                }
            }

        } else {
             return $this->error("交易信息被篡改");
        }
    }
}