<?php

namespace App\Services;

use App\Models\SystemTradeInfo;
use App\Models\User;
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class UserService
{
    /**
     * 获取用户余额
     *
     * @param null $user
     * @return array
     */
    public function getUserBalance($user = null)
    {
        $user = $user ?: (auth()->user() ?: (new User()));

        $balance = $user->balance;
        $protectedBalance = SystemTradeInfo::where('account', $user->user_name)->where('is_finished',
            cons('trade.is_finished.yes'))->where('finished_at', '>=', Carbon::now()->startOfDay())->sum('amount');
        $availableBalance = bcsub($balance, $protectedBalance, 2);

        return [
            'balance' => $balance,
            'protectedBalance' => $protectedBalance,
            'availableBalance' => $availableBalance
        ];
    }

    /**
     * 获取用户分类名
     *
     * @param null $user
     * @return mixed
     */
    public function getUserTypeName($user = null)
    {
        $user = $user ?: (auth()->user() ?: (new User()));
        $userTypes = array_flip(cons('user.type'));
        return array_get($userTypes, $user->type, head($userTypes));
    }
}