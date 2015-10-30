<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * 登录验证
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * 注册
     */
    public function register()
    {
        return view('auth.register');
    }

    public function guide(){
        return view('auth.guide');
    }


    /**
     * 退出登录
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function logout()
    {
        auth()->logout();
        return redirect('auth/guide');
    }
}
