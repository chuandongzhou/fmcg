<?php

namespace App\Http\Controllers\Index\Personal;


use App\Http\Controllers\Index\Controller;
use App\Http\Requests;
use App\Models\SystemTradeInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinanceController extends Controller
{

    /**
     * FinanceController constructor.
     */
    public function __construct()
    {
        $this->middleware('deposit:true');
    }
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getBalance(Request $request)
    {
        $data = $request->all();
        $user = auth()->user();
        $carbon = new Carbon();
        $data['start_at'] = isset($data['start_at']) ? $carbon->parse($data['start_at'])->startOfDay() : (string)$carbon->month($carbon->month - 1)->startOfDay();
        $data['end_at'] = isset($data['end_at']) ? $carbon->parse($data['end_at'])->endOfDay() : (string)$carbon->now()->endOfDay();

        $systemTradeInfo = SystemTradeInfo::whereHas('order', function ($order) use ($user) {
            $order->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)->orWhere('shop_id', $user->shop_id);
            });
        })->whereBetween('finished_at', [$data['start_at'], $data['end_at']]);
        $tradeBuilder = clone $systemTradeInfo;

        $todayBegin = Carbon::now()->startOfDay();

        // 受保护的金额
        $protectedBalance = $tradeBuilder->where('finished_at', '>=', $todayBegin)->sum('amount');

        $bankInfo = auth()->user()->userBanks;
        return view('index.personal.finance-balance', [
            'balance' => auth()->user()->balance,
            'protectedBalance' => $protectedBalance,
            'tradeInfo' => $systemTradeInfo->orderBy('finished_at', 'DESC')->paginate(30),
            'bankInfo' => $bankInfo,
            'data' => $data
        ]);
    }

    /**
     * 提现记录列表(含查询功能)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getWithdraw(Request $request)
    {
        $data = $request->all();
        $withdrawId = isset($data['id']) ? intval($data['id']) : '';

        $data['start_time'] = isset($data['start_time']) && $data['start_time'] != '' ? $data['start_time'] : date('Y-m-d',
            strtotime('-1 month'));;
        $data['end_time'] = isset($data['end_time']) && $data['end_time'] != '' ? $data['end_time'] : date('Y-m-d');
        $user = auth()->user();
        $withdraws = $user->withdraw()->with('userBanks');
        if ($withdrawId) {
            $withdraws = $withdraws->where('id', $withdrawId);
        }
        $withdraws = $withdraws->whereBetween('created_at',
            [$data['start_time'], (new Carbon($data['end_time']))->endOfDay()])->orderBy('id', 'DESC')->paginate();

        $protectedBalance = SystemTradeInfo::where('account', $user->user_name)->where('is_finished',
            cons('trade.is_finished.yes'))->where('finished_at', '>=', Carbon::now()->startOfDay())->sum('amount');
        return view('index.personal.finance-withdraw', [
            'balance' => $user->balance,
            'protectedBalance' => $protectedBalance,
            'data' => $data,
            'bankInfo' => $user->userBanks,
            'withdraws' => $withdraws, //提现记录
        ]);
    }
}
