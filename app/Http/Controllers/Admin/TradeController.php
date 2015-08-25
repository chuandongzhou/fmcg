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
        // TODO 搜索可优化
        if (isset($data['order_num']) || isset($data['trade_num'])) {
            if (isset($data['order_num']) && $data['order_num'] != '') {
                $map['order_num'] = $data['order_num'];
            }
            if (isset($data['trade_num']) && $data['trade_num']) {
                $map['trade_num'] = $data['trade_num'];
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
                ], $map)
        );
    }

}
