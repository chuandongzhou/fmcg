<?php

namespace App\Http\Controllers\Index\Personal;

use App\Models\SystemTradeInfo;
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
    public function index(Request $request)
    {

        $data = $request->all();
        $flag = 1;
        $withdrawId = isset($data['withdrawId']) ? intval($data['withdrawId']) : '';

        $data['start_time'] = isset($data['start_time']) && $data['start_time'] != '' ? $data['start_time'] : date('Y-m-d',
            strtotime('-1 month'));;
        $data['end_time'] = isset($data['end_time']) && $data['end_time'] != '' ? $data['end_time'] : date('Y-m-d');
        $user = auth()->user();
        $query = $user->withdraw()->with('userBanks');
        if ($withdrawId) {
            $withdraws = $query->find($withdrawId);
            $flag = 0;
        } else {
            $withdraws = $query->whereBetween('created_at',
                [$data['start_time'], (new Carbon($data['end_time']))->endOfDay()])->orderBy('id', 'DESC')->paginate();
        }

        $protectedBalance = SystemTradeInfo::where('account', auth()->user()->user_name)->where('is_finished',
            cons('trade.is_finished.yes'))->where('finished_at', '>=', Carbon::now()->startOfDay())->sum('amount');

        return view('index.personal.withdraw', [
            'flag' => $flag,
            'balance' => $user->balance,
            'protectedBalance' => $protectedBalance,
            'data' => $data,
            'bankInfo' => $user->userBanks,
            'withdraws' => $withdraws, //提现记录
        ]);
    }
}
