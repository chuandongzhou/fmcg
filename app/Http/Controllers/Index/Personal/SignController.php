<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/7
 * Time: 14:29
 */

namespace App\Http\Controllers\Index\Personal;

use App\Http\Controllers\Index\Controller;
use Illuminate\Http\Request;

class SignController extends Controller
{

    public function getIndex()
    {

        return view('index.personal.sign');
    }


    public function getRenew()
    {
        $renews = auth()->user()->renews()->paginate();
        return view('index.personal.sign-renew', compact('renews'));
    }

    public function postPrestorePay(Request $request)
    {
        $cost = (int)$request->input('cost');
        $user = auth()->user();
        if (!$cost || $cost <= 0) {
            return $this->error('请输入正确的金额');
        }
        if (($prestore = $user->prestore) < 0 && abs($prestore) > $cost) {
            return $this->error('金额不能小于欠费金额');
        }
        dd('缴纳成功！');
    }

    /**
     * 帐户续费处理
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\WeiHeng\Responses\IndexResponse
     */
    public function postExpirePay(Request $request)
    {
        $type = $request->input('type', 'user');
        $month = $request->input('month');
        $banks = cons()->lang('bank.type');
        $bankType = $request->input('bank_type', head($banks));
        if (!in_array($type, ['user', 'delivery', 'salesman']) || !array_get($banks, $bankType)) {
            return $this->error('支付失败，请重试');
        }

        if ($type == 'user') {
            $id = auth()->id();
            $months = cons()->valueLang('sign.expire_amount');
        } else {
            $id = $request->input('id');
            $months = cons()->valueLang('sign.worker_expire_amount');
        }

        if (!($cost = array_search($month, $months))) {
            return $this->error('选择的时间不正确');
        }

        return app('wechat.pay')->userExpire($id, $cost, $bankType, $type);
    }

    public function getRenewSuccess()
    {
        dd('续费成功');
    }
}
