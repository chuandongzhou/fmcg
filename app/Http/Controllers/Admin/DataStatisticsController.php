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
        $attrbutes = $request->all();
        $time = isset($attrbutes['time']) ? $attrbutes['time'] : (new Carbon)->formatLocalized('%Y-%m-%d');
        /**
         * 总用户数
         */
        $totleUser = User::select(DB::raw('count(*) as num , type'))->where('created_at', '<=',
            $time)->groupBy('type')->lists('num', 'type');

        $statistics = DataStatistics::where('created_at' , $time)->get();

    }


}
