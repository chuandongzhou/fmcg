<?php

namespace App\Http\Controllers\Index\Personal;


use App\Http\Controllers\Index\Controller;
use App\Http\Requests;
use App\Models\SystemTradeInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BalanceController extends Controller
{

    public function index(Request $request)
    {

        $data = $request->all();
        $startTime = isset($data['start_time']) && $data['start_time'] != '' ? $data['start_time'] : date('Y-m-d',
            strtotime('-1 month'));
        $endTime = isset($data['end_time']) && $data['end_time'] != '' ? $data['end_time'] : date('Y-m-d');
        $tradeInfo = SystemTradeInfo::select('trade_no', 'order_id','pay_type', 'type', 'amount', 'target_fee',
            'finished_at')->where('account', auth()->user()->user_name)->where('is_finished',cons('trade.is_finished.yes'))->whereBetween('finished_at',
            [$startTime, (new Carbon($endTime))->endOfDay()])->orderBy('finished_at', 'DESC')->paginate(30);
        $bankInfo = auth()->user()->userBanks;

        return view('index.personal.balance', [
            'balance' => auth()->user()->balance,
            'tradeInfo' => $tradeInfo,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'bankInfo' => $bankInfo,
        ]);
    }
}
