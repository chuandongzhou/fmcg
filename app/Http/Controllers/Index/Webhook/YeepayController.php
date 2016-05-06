<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/11/6
 * Time: 14:56
 */

namespace App\Http\Controllers\Index\Webhook;

use App\Http\Controllers\Index\Controller;
use App\Models\Order;
use App\Services\PayService;
use DB;
use Illuminate\Http\Request;

class YeepayController extends Controller
{

    public function anySuccess(Request $request)
    {
//	只有支付成功时易宝支付才会通知商户.
//支付成功回调有两次，都会通知到在线支付请求参数中的p8_Url上：浏览器重定向;服务器点对点通讯.

//	解析返回参数.
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

//	判断返回签名是否正确（True/False）
        $bRet = CheckHmac($r0_Cmd, $r1_Code, $r2_TrxId, $r3_Amt, $r4_Cur, $r5_Pid, $r6_Order, $r7_Uid, $r8_MP,
            $r9_BType, $hmac);
//	以上代码和变量不需要修改.


//	校验码正确.
        if ($bRet) {
            if ($r1_Code == "1") {

                //	需要比较返回的金额与商家数据库中订单的金额是否相等，只有相等的情况下才认为是交易成功.
                //	并且需要对返回的处理进行事务控制，进行记录的排它性处理，在接收到支付结果通知后，判断是否进行过业务逻辑处理，不要重复进行业务逻辑处理，防止对同一条交易重复发货的情况发生.

                if ($r9_BType == "1") {
                    //TODO:跳转至交易成功
                    return redirect(url('order-buy'));
                } elseif ($r9_BType == "2") {
                    //如果需要应答机制则必须回写流,以success开头,大小写不敏感.;
                    $field = $r8_MP == 'all' ? 'pid' : 'id';

                    info('应答机制成功' . $field);
                    $orders = Order::where($field, $r6_Order)->get()->each(function ($order) {
                        $order->setAppends([]);
                    });

                    $result = (new PayService)->addTradeInfo($orders, $r3_Amt, $rq_TargetFee, $r2_TrxId, 'yeepay',
                        $hmac);

                    return $result ? "success" : '';
                }
            }

        } else {
            return $this->error("交易信息被篡改");
        }
    }
}