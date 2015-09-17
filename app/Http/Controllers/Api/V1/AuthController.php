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
    public function postLogin(LoginRequest $request)
    {
        //TODO: 判断account
        $account = $request->input('account');
        $password = $request->input('password');

        if (auth()->attempt(['user_name' => $account, 'password' => $password])) {
            $this->success([]);
        }
        return $this->error('账号或密码错误');
    }
}