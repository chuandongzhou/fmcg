<?php

namespace App\Http\Controllers\Api\V1\ChildUser;

use App\Models\ChildUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        $childUser = ChildUser::where('account', $account)->first();

        if (!$childUser || !Hash::check($password, $childUser->password)) {
            return $this->invalidParam('password', '账号或密码错误');
        }

        //检测账号是否锁定
        if ($childUser->status != cons('status.on')) {
            return $this->invalidParam('password', '账号已锁定');
        }
        //检测账号是否过期
        if ($childUser->is_expire) {
            return $this->invalidParam('password', '账号已过期');
        }
        //有无节点
        $indexNode = $childUser->first_node;

        if (is_null($indexNode)) {
            return $this->invalidParam('password', '无权限登录');
        }

        $nowTime = Carbon::now();

        if ($childUser->fill(['last_login_at' => $nowTime, 'last_login_ip' => $request->ip()])->save()) {
            child_auth()->login($childUser, true);

            return $this->success(['childUser' => $childUser]);
        }
        return $this->invalidParam('password', '登录失败，请重试');

    }


}
