<?php

namespace App\Http\Controllers\Admin;

use App\Models\Withdraw;
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
        $startAt = isset($data['started_at']) && $data['started_at'] != '' ? $data['started_at'] : date('Y-m-d',
            strtotime('-1 month'));;
        $endAt = isset($data['end_at']) && $data['end_at'] != '' ? $data['end_at'] : date('Y-m-d');
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
    {//TODO:redis通知功能，待完成
        $withdrawId = intval($request->input('withdraw_id'));
        $item = Withdraw::where('status', cons('withdraw.review'))->find($withdrawId);
        if ($item) {
            $item->update(['status' => cons('withdraw.pass'), 'pass_at' => Carbon::now()]);

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
            $item->update([
                'status' => cons('withdraw.payment'),
                'payment_at' => Carbon::now(),
                'trade_no' => trim($data['trade_no'])
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
            $item->update([
                'status' => cons('withdraw.failed'),
                'failed_at' => Carbon::now(),
                'reason' => trim($data['reason'])
            ]);

            return $this->success('操作成功');
        }

        return $this->error('操作失败');
    }
}
