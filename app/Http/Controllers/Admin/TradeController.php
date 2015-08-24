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
        $attributes = array_filter($request->all());
        $map = [];
        $trades = [];
        if (isset($attributes['order_num']) || isset($attributes['trade_num'])) {
            if (isset($attributes['order_num']) && $attributes['order_num'] != '') {
                $map['order_num'] = $attributes['order_num'];
            }
            if (isset($attributes['trade_num']) && $attributes['trade_num']) {
                $map['trade_num'] = $attributes['trade_num'];
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
