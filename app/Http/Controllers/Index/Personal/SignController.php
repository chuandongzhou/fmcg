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

    public function getIndex(){
        return view('index.personal.sign');
    }


    public function getRenew()
    {
        $renews = auth()->user()->renews()->paginate();
        return view('index.personal.sign-renew', compact('renews'));
    }

    public function getDepositPay()
    {
        dd('缴纳成功！');
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

    public function postExpirePay(Request $request)
    {
        $months = cons()->valueLang('sign.expire_amount');
        $month = $request->input('month');

        if (!($cost = array_search($month, $months))) {
            return $this->error('选择的时间不正确');
        }

        dd('续期成功');
    }
}
