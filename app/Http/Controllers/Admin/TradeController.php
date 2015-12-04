<?php

namespace App\Http\Controllers\Admin;

use App\Models\SystemTradeInfo;
use Illuminate\Http\Request;

use App\Http\Requests;

class TradeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex(Request $request)
    {
        $data = array_filter($request->all());
        $map = [];
        $trades = [];
        // TODO: 搜索可优化
        if (isset($data['order_id']) || isset($data['trade_no'])) {
            if (isset($data['order_id']) && $data['order_id'] != '') {
                $map['order_id'] = $data['order_id'];
            }
            if (isset($data['trade_no']) && $data['trade_no']) {
                $map['trade_no'] = $data['trade_no'];
            }
            if (isset($attributes['pay_type']) && $attributes['pay_type']) {
                $map['pay_type'] = $attributes['pay_type'];
            }
            $trades = SystemTradeInfo::where($map)->paginate();
        }

        return view('admin.trade.select',
            array_merge(
                [
                    'trades' => $trades,
                ], $data)
        );
    }

}
