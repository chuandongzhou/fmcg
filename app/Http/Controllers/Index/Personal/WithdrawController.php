<?php

namespace App\Http\Controllers\Index\Personal;

use App\Models\UserBank;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Api\v1\Controller;

class WithdrawController extends Controller
{

    /**
     * 提现记录列表(含查询功能)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getIndex(Request $request)
    {

        $data = $request->all();
        $flag = 1;
        $withdrawId = isset($data['withdrawId']) ? intval($data['withdrawId']) : '';

        $startTime = isset($data['start_time']) && $data['start_time'] != '' ? $data['start_time'] : date('Y-m-d',
            strtotime('-1 month'));;
        $endTime = isset($data['end_time']) && $data['end_time'] != '' ? $data['end_time'] : date('Y-m-d');
        $user = auth()->user();
        $query = Withdraw::with('userBanks')->where('user_id', $user->id);
        if ($withdrawId) {
            $withdraws = $query->find($withdrawId);
            $flag = 0;
        } else {
            $withdraws = $query->whereBetween('created_at',
                [$startTime, (new Carbon($endTime))->endOfDay()])->orderBy('id', 'DESC')->paginate(30);
        }

        return view('index.personal.withdraw', [
            'flag' => $flag,
            'balance' => $user->balance,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'bankInfo' => $user->userBanks,
            'withdraws' => $withdraws, //提现记录
        ]);
    }

    /**
     * 提现操作
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postAddWithdraw(Request $request)
    {
        //验证提现金额与提现账户
        $this->validate($request, [
            'amount' => 'required|Numeric',
            'bank_id' => 'required|Numeric'
        ]);
        $amount = $request->input('amount');
        $bankId = $request->input('bank_id');
        $user = auth()->user();
        if ($amount > $user->balance) {
            return $this->error('体现金额不合法,请确认后再试');
        }
        if (!UserBank::where('user_id', $user->id)->find($bankId)) {
            return $this->error('该提现账号不存在,请确认后再试');
        }
        //修改账户余额
        $user->balance = $user->balance - $amount;
        $user->save();

        //新建提现记录
        return Withdraw::create([
            'user_id' => $user->id,
            'user_bank_id' => $bankId,
            'amount' => $amount,
            'created_at' => Carbon::now()
        ]) ? $this->success('申请成功,请等待审核') : $this->error('申请遇到问题,请稍后再试');

    }
}
