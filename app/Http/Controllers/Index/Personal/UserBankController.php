<?php

namespace App\Http\Controllers\Index\Personal;

use App\Http\Controllers\Index\Controller;

use App\Http\Requests;
use App\Models\UserBank;

class UserBankController extends Controller
{
    public function index()
    {
        $userBanks = auth()->user()->userBanks()->get()->toArray(); //商店详情
        return view('index.personal.bank-index',
            ['userBanks' => $userBanks]);
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
