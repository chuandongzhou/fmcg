<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DataStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-statistics';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $nowTime = Carbon::now();
        $userType = cons('user.type');
        $monthAgo = $nowTime->copy()->subDays(30);
        // 活跃用户数
        $activeUser = User::select(DB::raw('count(*) as num,type'))->where('last_login_at', '>',
            $monthAgo)->groupBy('type')->lists('num', 'type');
        $activeUserArr = [
            array_get($activeUser, array_get($userType, 'wholesalers'), 0),
            array_get($activeUser, array_get($userType, 'retailer'), 0),
            array_get($activeUser, array_get($userType, 'retailer'), 0)
        ];

        $dayAgo = $nowTime->copy()->subDay();
        //今日注册数
        $regCount = User::select(DB::raw('count(*) as num,type'))->where('created_at', '>', $dayAgo)->lists('num',
            'type');

        $wholesalersReg = array_get($regCount, array_get($userType, 'wholesalers'), 0);
        $supplierReg = array_get($regCount, array_get($userType, 'supplier'), 0);
        $retailerReg = array_get($regCount, array_get($userType, 'retailer'), 0);

        //今日登录数
        $loginCount = User::select(DB::raw('count(*) as num , type'))->where('last_login_at', '>',
            $dayAgo)->lists('num', 'type');

        $wholesalersLogin = array_get($loginCount, array_get($userType, 'wholesalers'), 0);
        $supplierLogin = array_get($loginCount, array_get($userType, 'supplier'), 0);
        $retailerLogin = array_get($loginCount, array_get($userType, 'retailer'), 0);
        \App\Models\DataStatistics::create([
            'active_user' => implode(',', $activeUserArr),
            'wholesaler_login_num' => $wholesalersLogin,
            'retailer_login_num' => $retailerLogin,
            'supplier_login_num' => $supplierLogin,
            'wholesaler_reg_num' => $wholesalersReg,
            'retailer_reg_num' => $retailerReg,
            'supplier_reg_num' => $supplierReg,
            'created_at' => new Carbon()
        ]);
    }
}
