<?php

namespace App\Http\Controllers\Admin;

use App\Models\SystemTradeInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Maatwebsite\Excel\Facades\Excel;

class SystemTradeInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex(Request $request)
    {
        $attributes = array_filter($request->all());

        $result = $this->getTrade($attributes);

        return view('admin.trade.index',

            [
                'trades' => $result['trades'],
                'linkUrl' => http_build_query($attributes),
                'data' => $result['map']
            ]
        );
    }

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function getExportToExcel(Request $request)
    {
        $attributes = array_filter($request->all());
        $trades = $this->getTrade($attributes)['trades']->toArray();
        Excel::create('平台交易信息', function ($excel) use ($trades) {
            $excel->sheet('Excel sheet', function ($sheet) use ($trades) {
                $sheet->mergeCells('A1:L1');//merge for title
                $sheet->row(1, function ($row) {
                    $row->setFontSize(18);
                    $row->setAlignment('center');
                });
                $sheet->row(1, ['平台交易信息']);//add title
                $sub_titles = [
                    '类型',
                    '支付平台',
                    '商家账号',
                    '订单号',
                    '交易号',
                    '支付结果',
                    '支付金额',
                    '交易币种',
                    '交易返回类型',
                    '交易成功时间',
                    '交易结果通知',
                    'hamc'
                ];
                $sheet->setWidth([
                    'A' => 10,
                    'B' => 10,
                    'C' => 15,
                    'D' => 15,
                    'E' => 15,
                    'F' => 10,
                    'G' => 10,
                    'H' => 10,
                    'I' => 15,
                    'J' => 20,
                    'K' => 20,
                    'L' => 30
                ]);

                $sheet->row('2', $sub_titles);
                foreach ($trades['data'] as $trade) {
                    $array = [
                        'type' => cons()->valueLang('trade.type', $trade['type']),
                        'pay_type' => cons()->valueLang('trade.pay_type', $trade['pay_type']),
                        'account' => $trade['account'],
                        'order_id' => $trade['order_id'],
                        'trade_no' => $trade['trade_no'],
                        'pay_status' => cons()->valueLang('trade.pay_status', $trade['pay_status']),
                        'amount' => $trade['amount'],
                        'trade_currency' => cons()->valueLang('trade.trade_currency', $trade['trade_currency']),
                        'callback_type' => $trade['callback_type'],
                        'success_at' => $trade['success_at'],
                        'notice_at' => $trade['notice_at'],
                        'hmac' => $trade['hmac']
                    ];
                    $sheet->appendRow($array);
                }
            });

        })->export('xls');

    }

    private function getTrade($attributes)
    {
        $map = [];
        if (isset($attributes['order_id']) && $attributes['order_id'] != '') {
            $map['order_id'] = $attributes['order_id'];
        }
        if (isset($attributes['trade_no']) && $attributes['trade_no']) {
            $map['trade_no'] = $attributes['trade_no'];
        }

        if (isset($attributes['account']) && $attributes['account']) {
            $map['account'] = $attributes['account'];
        }
        if (isset($attributes['pay_type']) && $attributes['pay_type']) {
            $map['pay_type'] = $attributes['pay_type'];
        }
        $time['started_at'] = isset($attributes['started_at']) ? $attributes['started_at'] : Carbon::now()->startOfMonth();
        $time['ended_at'] = isset($attributes['ended_at']) ? $attributes['ended_at'] : Carbon::now();

        $trades = SystemTradeInfo::where($map)->whereBetween('success_at',
            [$time['started_at'], $time['ended_at']])->paginate();

        return [
            'trades' => $trades,
            'map' => array_merge($map, $time)
        ];
    }
}
