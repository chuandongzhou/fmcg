<?php

namespace App\Http\Controllers\Api\V1\Personal;

use App\Http\Requests\Api\v1\CreateWithdrawRequest;
use App\Models\SystemTradeInfo;
use App\Models\UserBank;
use App\Models\Withdraw;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\Controller;

class FinanceController extends Controller
{

    /**
     * 提现记录列表(含查询功能)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getWithdraw(Request $request)
    {
        $data = $request->all();
        $startTime = isset($data['start_time']) && $data['start_time'] != '' ? $data['start_time'] : date('Y-m-d',
            strtotime('-1 month'));;
        $endTime = isset($data['end_time']) && $data['end_time'] != '' ? $data['end_time'] : date('Y-m-d');
        $user = auth()->user();
        $withdraws = $user->withdraw()->with('userBanks')->whereBetween('created_at',
            [$startTime, (new Carbon($endTime))->endOfDay()])->orderBy('id', 'DESC')->paginate()->toArray();

        return $this->success([
            'withdraws' => $withdraws, //提现记录
        ]);
    }

    /**
     * 交易流水帐
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getDaybook (Request $request){
        $data = $request->all();

        $dayBook = SystemTradeInfo::select('trade_no', 'order_id', 'pay_type', 'type', 'amount', 'target_fee',
            'finished_at')->where('account', auth()->user()->user_name)->where('is_finished',
            cons('trade.is_finished.yes'));
        if (isset($data['start_time'])) {
            $data['end_time'] = isset($data['end_time']) && $data['end_time'] != '' ? $data['end_time'] : date('Y-m-d');

            $dayBook->whereBetween('finished_at',
                [$data['start_time'], (new Carbon($data['end_time']))->endOfDay()]);
        }
        return $this->success([
            'dayBook' => $dayBook->orderBy('id', 'DESC')->paginate()->toArray()
        ]);
    }

    /**
     * 获取帐户余额以及受保护的金额
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getBalance(){
        return $this->success((new UserService())->getUserBalance());
    }

    /**
     * 提现操作
     *
     * @param \App\Http\Requests\Api\v1\CreateWithdrawRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postAddWithdraw(CreateWithdrawRequest $request)
    {
        $amount = $request->input('amount');
        $bankId = $request->input('bank_id');
        $user = auth()->user();

        //获取该用户受保护的金额

        $protectedBalance = SystemTradeInfo::where('account', auth()->user()->user_name)->where('is_finished',
            cons('trade.is_finished.yes'))->where('finished_at', '>=',  Carbon::now()->startOfDay())->sum('amount');

        if ($amount > $user->balance - $protectedBalance) {
            return $this->error('可提现金额不足');
        }
        if (!$bank = UserBank::where('user_id', $user->id)->find($bankId)) {
            return $this->error('该提现账号不存在');
        }
        //修改账户余额
        $user->balance = $user->balance - $amount;
        $user->save();

        //新建提现记录
        return Withdraw::create([
            'user_id' => $user->id,
            'user_bank_id' => $bankId,
            'amount' => $amount,
            'created_at' => Carbon::now(),
            'card_holder' => $bank->card_holder,
            'card_number' => $bank->card_number,
            'card_type' => $bank->card_type,
            'card_address' => $bank->card_address
        ]) ? $this->success('申请成功,请等待审核') : $this->error('申请遇到问题');

    }
}
