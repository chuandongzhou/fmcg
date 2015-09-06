<?php

namespace App\Http\Controllers\Index\personal;

use App\Http\Controllers\Index\Controller;

use App\Http\Requests;
use App\Models\User;
use App\Models\UserBank;

class UserBankController extends Controller
{
    protected $shopId = 1;

    public function index()
    {
        $userBanks = User::with('userBanks')->where('id', 1)->first()->toArray(); //商店详情
        $defaultBank = array_filter($userBanks['user_banks'], function ($bank) {
            return $bank['is_default'] == 1;
        });
        array_pull($userBanks['user_banks'], array_keys($defaultBank)[0]);
        return view('index.personal.bank-index', ['userBanks' => $userBanks['user_banks'], 'defaultBank' => $defaultBank]);
    }

    /**
     * 创建银行
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('index.personal.bank', ['userBank' => new UserBank]);
    }

    /**
     * 编辑
     *
     * @param $userBank
     * @return \Illuminate\View\View
     */
    public function edit($userBank)
    {
        return view('index.personal.bank', ['userBank' => $userBank]);
    }
}
