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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        $data = array_except($request->all(), 'page');

        $trades = SystemTradeInfo::where($data)->paginate();


        return view('admin.trade.select',
            [
                'trades' => $trades,
                'data' => $data
            ]
        );
    }

}
