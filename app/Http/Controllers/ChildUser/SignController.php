<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/7
 * Time: 14:29
 */

namespace App\Http\Controllers\ChildUser;

use Illuminate\Http\Request;

class SignController extends Controller
{

    public function getIndex()
    {
        return view('index.personal.sign');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getRenew()
    {
        $user = child_auth()->user()->user;
        $renews = $user->renews()->paginate();
        return view('child-user.sign.renew', compact('renews', 'user'));
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
