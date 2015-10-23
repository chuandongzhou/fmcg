<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/17
 * Time: 14:22
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\v1\LoginRequest;
use App\Http\Requests\Api\v1\RegisterRequest;
use App\Models\User;

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
        // $type = $request->input('type');

        if (auth()->viaRemember() || auth()->attempt([
                'user_name' => $account,
                'password' => $password
                /*, 'type' => $type*/
            ], true)
        ) {
            return $this->success(auth()->user()->load('shop')->toArray());
        }
        return $this->error('账号或密码错误');
    }

    /**
     * 注册
     *
     * @param \App\Http\Requests\Api\v1\RegisterRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     * @throws \Exception
     */
    public function postRegister(RegisterRequest $request)
    {
        $userInput = $request->only('username', 'password', 'type');
        $user = User::Create($userInput);
        if ($user->exists) {
            //商店
            $shopInput = $request->except('username', 'password', 'type');
            $shopModel = $user->shop();

            if ($shopModel->create($shopInput)->exists) {
                return $this->success('添加用户成功');
            } else {
                $user->delete();
                return $this->error('添加用户时遇到问题');
            }
        }
        return $this->error('添加用户时遇到问题');
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