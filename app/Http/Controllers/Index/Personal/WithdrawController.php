<?php

namespace App\Http\Controllers\Index\Personal;

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
                [$startTime, (new Carbon($endTime))->endOfDay()])->orderBy('id', 'DESC')->paginate();
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
}
