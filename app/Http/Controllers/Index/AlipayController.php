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
use Gate;
use WeiHeng\Alipay\AlipaySubmit;

class AlipayController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param $orderId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index(Request $request, $orderId)
    {
        $type = $request->input('type');
        $field = $type == 'all' ? 'pid' : 'id';
        $orders = Order::where($field, $orderId)->get();

        if (Gate::denies('validate-online-orders', $orders)) {
            return redirect('order-buy');
        }

        $alipayConf = config('alipay');
        $alipayConf['notify_url'] = url($alipayConf['notify_url']);
        $alipayConf['return_url'] = url($alipayConf['return_url']);
        //构造要请求的参数数组，无需改动
        $parameter = array(
            'service' => $alipayConf['service'],
            'partner' => $alipayConf['partner'],
            'seller_id' => $alipayConf['seller_id'],
            'payment_type' => $alipayConf['payment_type'],
            'notify_url' => $alipayConf['notify_url'],
            'return_url' => $alipayConf['return_url'],

            'anti_phishing_key' => $alipayConf['anti_phishing_key'],
            'exter_invoke_ip' => $alipayConf['exter_invoke_ip'],
            'out_trade_no' => $orderId,
            'subject' => '成都订百达科技有限公司',
            'total_fee' => $orders->pluck('price')->sum(),
            'body' => '',
            'extra_common_param' => $field,
            //公共回传参数
            '_input_charset' => trim(strtolower($alipayConf['input_charset']))
            //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
            //如'参数名'=>'参数值'
            //建立请求
        );

        $alipaySubmit = new AlipaySubmit($alipayConf);
        return $alipaySubmit->buildRequestForm($parameter, 'post', '确认');
    }
}