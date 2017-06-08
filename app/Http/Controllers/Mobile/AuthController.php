<?php

namespace App\Http\Controllers\Mobile;


class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest', ['except' => ['logout']]);
    }

    /**
     * 登录验证
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function login()
    {
        return view('mobile.auth.login');
    }

    /**
     * 创建帐户
     */
    public function registerAccount()
    {
        return view('mobile.auth.register-account');
    }

    /**
     * 设置帐户密码
     */
    public function registerPassword()
    {
        $user = session('user');
        if (empty($user)) {
            return redirect(url('auth/register-account'));
        }
        return view('mobile.auth.register-password', ['user' => $user]);
    }

    /**
     * 创建店铺
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function registerShop()
    {
        $user = session('user');
        if (empty($user) || !array_get($user, 'password')) {
            return redirect(url('auth/register-account'));
        }

        return view('mobile.auth.register-shop', compact('user'));
    }

    /**
     * 注册成功页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function registerSuccess()
    {
        $user = session('user');
        if (empty($user) || !array_get($user, 'password')) {
            return redirect(url('auth/register-account'));
        }
        session()->forget('user');
        return view('mobile.auth.register-success', ['userName' => $user['user_name'], 'userType' => $user['type']]);

    }

    public function forgetPassword()
    {
        return view('mobile.auth.forget-password');
    }

    /**
     * 退出登录
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        auth()->logout();
        return redirect('auth/login');
    }


}
