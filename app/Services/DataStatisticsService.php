<?php

namespace App\Services;

use App\Models\User;
use DB;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class DataStatisticsService
{
    /**
     * 数据统计
     *
     * @param \Carbon\Carbon $nowTime
     * @return array
     */
    public static function getTodayDataStatistics($nowTime)
    {
        $userType = cons('user.type');
        $monthAgo = $nowTime->copy()->subDays(30);
        // 活跃用户数
        $activeUser = User::select(DB::raw('count(*) as num,type'))->where('last_login_at', '>',
            $monthAgo)->groupBy('type')->lists('num', 'type');
        $activeUserArr = [
            array_get($activeUser, array_get($userType, 'wholesaler'), 0),
            array_get($activeUser, array_get($userType, 'retailer'), 0),
            array_get($activeUser, array_get($userType, 'retailer'), 0)
        ];

        $dayAgo = $nowTime->copy()->subDay()->startOfDay();
        //今日注册数
        $regCount = User::select(DB::raw('count(*) as num,type'))->where('created_at', '>', $dayAgo)->lists('num',
            'type');

        $wholesalersReg = array_get($regCount, array_get($userType, 'wholesaler'), 0);
        $supplierReg = array_get($regCount, array_get($userType, 'supplier'), 0);
        $retailerReg = array_get($regCount, array_get($userType, 'retailer'), 0);

        //今日登录数
        $loginCount = User::select(DB::raw('count(*) as num , type'))->where('last_login_at', '>',
            $dayAgo)->lists('num', 'type');

        $wholesalersLogin = array_get($loginCount, array_get($userType, 'wholesaler'), 0);
        $supplierLogin = array_get($loginCount, array_get($userType, 'supplier'), 0);
        $retailerLogin = array_get($loginCount, array_get($userType, 'retailer'), 0);

        return [
            'active_user' => $activeUserArr,
            'wholesaler_login_num' => $wholesalersLogin,
            'retailer_login_num' => $retailerLogin,
            'supplier_login_num' => $supplierLogin,
            'wholesaler_reg_num' => $wholesalersReg,
            'retailer_reg_num' => $retailerReg,
            'supplier_reg_num' => $supplierReg
        ];
    }
}