<?php

namespace App\Http\Controllers\Admin;

use App\Models\DataStatistics;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class DataStatisticsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex(Request $request)
    {
        $time = (new Carbon($request->input('time')))->endOfDay();
        /**
         * 总用户数
         */
        $totleUser = User::select(DB::raw('count(*) as num , type'))->where('created_at', '<=',
            $time)->groupBy('type')->lists('num', 'type');

        $statistics = DataStatistics::where('created_at', $time->toDateString())->first();
        //历史最高注册数和登录数

        /**
         *  'wholesalers' => 1,       //批发商
         * 'retailer' => 2,       //零售商
         * 'supplier' => 3,
         */
        $maxArray = [
            'max_wholesaler_login_num' => DataStatistics::where('created_at', '<=',
                $time)->orderBy('wholesaler_login_num', 'desc')->select('wholesaler_login_num', 'created_at')->first(),
            'max_retailer_login_num' => DataStatistics::where('created_at', '<=',
                $time)->orderBy('retailer_login_num', 'desc')->select('retailer_login_num', 'created_at')->first(),
            'max_supplier_login_num' => DataStatistics::where('created_at', '<=',
                $time)->orderBy('supplier_login_num', 'desc')->select('supplier_login_num', 'created_at')->first(),
            'max_wholesaler_reg_num' => DataStatistics::where('created_at', '<=',
                $time)->orderBy('wholesaler_reg_num', 'desc')->select('wholesaler_reg_num', 'created_at')->first(),
            'max_retailer_reg_num' => DataStatistics::where('created_at', '<=',
                $time)->orderBy('retailer_reg_num', 'desc')->select('retailer_reg_num', 'created_at')->first(),
            'max_supplier_reg_num' => DataStatistics::where('created_at', '<=',
                $time)->orderBy('supplier_reg_num', 'desc')->select('supplier_reg_num', 'created_at')->first(),
        ];
        return view('admin.statistics.index', ['totleUser' => $totleUser, 'statistics' => $statistics, 'maxArray' => $maxArray , 'time' => $time]);
    }
    // TODO 导出的方法
    // 每日订单统计
}
