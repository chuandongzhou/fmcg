<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/10/20
 * Time: 14:49
 */
namespace App\Http\Controllers\Admin;

use App\Http\Requests\Api\v1\LoginRequest;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.guest', ['except' => 'getLogout']);
    }

    /**
     * 后台登录页
     *
     * @return \Illuminate\View\View
     */
    public function getLogin()
    {
        return view('admin.auth.login');
    }

    /**
     * 后台登录处理
     *
     * @param \App\Http\Requests\Api\v1\LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postLogin(LoginRequest $request)
    {
        $account = $request->input('account');
        $password = $request->input('password');
        if (admin_auth()->attempt(['name' => $account, 'password' => $password])) {
            return redirect()->intended('admin');
        }
        return redirect('admin/auth/login')->with('message', '账号或密码错误');
    }

    /**
     * 退出登录
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getLogout()
    {
        admin_auth()->logout();
        return redirect('admin/auth/login');
    }
}