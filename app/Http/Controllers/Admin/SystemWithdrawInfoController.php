<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Withdraw;
use App\Services\RedisService;
use Carbon\Carbon;
use Illuminate\Http\Request;


class SystemWithdrawInfoController extends Controller
{
    /**
     * 获取提现信息
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        $data = $request->all();
        $withdrawId = isset($data['withdraw_id']) && $data['withdraw_id'] != '' ? intval($data['withdraw_id']) : '';
        $tradeNo = isset($data['trade_no']) && $data['trade_no'] != '' ? trim($data['trade_no']) : '';
        $userName = isset($data['user_name']) && $data['user_name'] != '' ? trim($data['user_name']) : '';
        $data['started_at'] = isset($data['started_at']) && $data['started_at'] != '' ? $data['started_at'] : (string)Carbon::now()->startOfMonth();
        $data['end_at'] = isset($data['end_at']) && $data['end_at'] != '' ? $data['end_at'] : (string)Carbon::now();
        $query = Withdraw::with('userBanks', 'user');
        if ($withdrawId) {
            $query->where('id', $withdrawId);
        }
        if ($tradeNo) {
            $query->where('trade_no', $tradeNo);
        }
        if ($userName) {
            $query->whereHas('user', function ($query) use ($userName) {
                $query->where('user_name', $userName);
            });
        }
        $withdraws = $query->whereBetween('created_at', [$data['started_at'], (new Carbon($data['end_at']))->endOfDay()])->orderBy('id',
            'DESC')->paginate();

        return view('admin.withdraw.index', [
            'withdraws' => $withdraws,
            'data' => $data
        ]);
    }

    /**
     * 通过审核
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putPass(Request $request)
    {
        $withdrawId = intval($request->input('withdraw_id'));
        $item = Withdraw::where('status', cons('withdraw.review'))->find($withdrawId);
        if ($item) {
            $item->fill(['status' => cons('withdraw.pass'), 'pass_at' => Carbon::now()])->save();

            return $this->success('操作成功');
        }

        return $this->error('操作失败');
    }

    /**
     * 已打款
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putPayment(Request $request)
    {
        $data = $request->all();
        $item = Withdraw::where('status', cons('withdraw.pass'))->find(intval($data['withdraw_id']));

        if ($item) {
            $item->fill([
                'status' => cons('withdraw.payment'),
                'payment_at' => Carbon::now(),
                'trade_no' => trim($data['trade_no'])
            ])->save();
            //启动通知
            $redisKey = 'push:withdraw:' . $item->user_id;
            $redisVal = '您的提现订单:' . $item->id . ',' . cons()->lang('push_msg.review_payment');

            (new RedisService)->setRedis($redisKey, $redisVal, cons('push_time.msg_life'));

            app('pushbox.sms')->send('withdraw', $item->user->backup_mobile,
                [
                    'withdraw_id' => $data['withdraw_id'],
                    'trade_no' => $data['trade_no'],
                ]);
            return $this->success('操作成功');
        }

        return $this->error('操作失败');
    }

    /**
     * 审核不通过
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putFailed(Request $request)
    {
        $data = $request->all();
        $item = Withdraw::where('status', cons('withdraw.review'))->find(intval($data['withdraw_id']));
        if ($item) {
            $item->fill([
                'status' => cons('withdraw.failed'),
                'failed_at' => Carbon::now(),
                'reason' => trim($data['reason'])
            ])->save();
            //返还扣掉的钱
            $user = User::find($item->user_id);
            $user->balance = $user->balance + $item->amount;
            $user->save();
            //启动通知
            $redisKey = 'push:withdraw:' . $item->user_id;
            $redisVal = '您的提现订单:' . $item->id . ',' . cons()->lang('push_msg.review_failed');
            (new RedisService)->setRedis($redisKey, $redisVal ,cons('push_time.msg_life'));

            return $this->success('操作成功');
        }

        return $this->error('操作失败');
    }

}
