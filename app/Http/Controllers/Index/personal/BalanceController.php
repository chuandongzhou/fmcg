<?php

namespace App\Http\Controllers\Index\personal;

use App\Http\Controllers\Api\v1\Controller;

use App\Http\Requests;
use App\Models\SystemTradeInfo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceController extends Controller
{

    public function index(Request $request)
    {

        //TODO: 登录
        $userId = 1;

        $data = $request->all();
        $startTime = isset($data['start_time']) && $data['start_time'] != '' ? $data['start_time'] : date('Y-m-d',
            strtotime('-1 month'));;
        $endTime = isset($data['end_time']) && $data['end_time'] != '' ? $data['end_time'] : date('Y-m-d');
        $balance = User::where('id', 1)->pluck('balance');


        $tradeInfo = SystemTradeInfo::select('trade_num', 'order_num', 'type' , 'amount')->where('account',
            'wholesalers')->whereBetween('success_at', [$startTime, (new Carbon($endTime))->endOfDay()])->paginate(30);
        return view('index.personal.balance',
            [
                'balance' => $balance,
                'tradeInfo' => $tradeInfo,
                'startTime' => $startTime,
                'endTime' => $endTime
            ]);
    }

}
