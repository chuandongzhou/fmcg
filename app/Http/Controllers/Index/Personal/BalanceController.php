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

        $tradeInfo = SystemTradeInfo::select('trade_no', 'order_id', 'pay_type', 'type', 'amount', 'target_fee',
            'finished_at')->where('account', auth()->user()->user_name)->where('is_finished',
            cons('trade.is_finished.yes'));
        if (isset($data['start_time'])) {
            $data['end_time'] = isset($data['end_time']) && $data['end_time'] != '' ? $data['end_time'] : date('Y-m-d');

            $tradeInfo->whereBetween('finished_at',
                [$data['start_time'], (new Carbon($data['end_time']))->endOfDay()]);
        }
        $bankInfo = auth()->user()->userBanks;

        return view('index.personal.balance', [
            'balance' => auth()->user()->balance,
            'tradeInfo' => $tradeInfo->orderBy('finished_at', 'DESC')->paginate(30),
            'bankInfo' => $bankInfo,
            'data' => $data
        ]);
    }
}
