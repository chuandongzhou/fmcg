<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/17
 * Time: 14:22
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\v1\LoginRequest;

class AuthController extends Controller
{
    /**
     * 登录
     *
     * @param \App\Http\Requests\Api\v1\LoginRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postLogin(LoginRequest $request)
    {
        //TODO: 判断account
        $account = $request->input('account');
        $password = $request->input('password');
        $type = $request->input('type');

        if (auth()->attempt(['user_name' => $account, 'password' => $password, 'type' => $type])) {
            return $this->success('登录成功');
        }
        return $this->error('账号或密码错误');
    }

    /**
     * 退出登录
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getLogout()
    {
        auth()->logout();
        return $this->success();
    }
}