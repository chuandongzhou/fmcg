<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;


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
        $startAt = isset($data['started_at']) && $data['started_at'] != '' ? $data['started_at'] : Carbon::now()->startOfMonth();
        $endAt = isset($data['end_at']) && $data['end_at'] != '' ? $data['end_at'] : Carbon::now();
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
        $withdraws = $query->whereBetween('created_at', [$startAt, (new Carbon($endAt))->endOfDay()])->orderBy('id',
            'DESC')->paginate();

        return view('admin.withdraw.index', [
            'startedAt' => $startAt,
            'endAt' => $endAt,
            'withdraws' => $withdraws,
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
            $this->_pushMsg($item->user_id, $item->id, 'review_payment');

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
            $this->_pushMsg($item->user_id, $item->id, 'review_failed');

            return $this->success('操作成功');
        }

        return $this->error('操作失败');
    }

    /**
     * 添加推送信息
     *
     * @param $userId
     * @param $orderId
     * @param $msg
     */
    private function _pushMsg($userId, $orderId, $msg)
    {
        //启动通知
        $redis = Redis::connection();
        $redisKey = 'push:withdraw:' . $userId;
        $redis->set($redisKey, '您的提现订单:' . $orderId . ',' . cons()->lang('push_msg.' . $msg));
        $redis->expire($redisKey, cons('push_time.msg_life'));
    }
}
