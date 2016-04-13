<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;

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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function login(Request $request)
    {
//        $type = $request->input('type');
//
//        if (!$type || !$typeId = array_get(cons('user.type') ,$type)) {
//            return redirect('auth/guide');
//        }
        return view('auth.login');
    }

    /**
     * 注册
     */
    public function register()
    {
        return view('auth.register');
    }

    /**
     * 引导页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function guide()
    {
        return view('auth.guide');
    }

    /**
     * 注册成功
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function regSuccess(Request $request)
    {
        if (!session('shop.name')) {
            return $this->error('请先注册', 'auth/register');
        }
        return view('auth.reg-success');
    }

    /**
     * 退出登录
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function logout()
    {
        auth()->logout();
        return redirect('auth/login');
    }
}
