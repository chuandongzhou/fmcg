<?php

namespace App\Http\Controllers\Api\V1\Business;

use App\Models\Salesman;
use App\Services\BusinessService;
use App\Services\SalesmanTargetService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Api\V1\Controller;
use Hash;

class AuthController extends Controller
{
    /**
     * 登录
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function login(Request $request)
    {
        $account = $request->input('account');
        $password = $request->input('password');
        if (!$account || !$password) {
            return $this->invalidParam('password', '账号或密码不能为空');
        }

        $salesman = Salesman::where('account', $account)->with('shop.shopAddress')->first();

        if (!$salesman || !Hash::check($password, $salesman->password)) {
            return $this->invalidParam('password', '账号或密码错误');
        }

        //检测账号是否锁定
        if ($salesman->status != cons('status.on')) {
            return $this->invalidParam('password', '账号已锁定');
        }
        //检测账号是否过期
        if ($salesman->expire->isPast()) {
            return $this->invalidParam('password', '账号已过期');
        }


        $nowTime = Carbon::now();

        $salesman->setAppends(['shop_type', 'avatar_url']);

        if ($salesman->fill(['last_login_at' => $nowTime, 'last_login_ip' => $request->ip()])->save()) {
            salesman_auth()->login($salesman, true);

            return $this->success(['salesman' => $salesman]);
        }
        return $this->invalidParam('password', '登录失败，请重试');

    }

    /**
     * 退出登录
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function logout()
    {
        salesman_auth()->logout();
        return $this->success([]);
    }


}
