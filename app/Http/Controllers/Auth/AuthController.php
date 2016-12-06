<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Germey\Geetest\CaptchaGeetest;

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

    use CaptchaGeetest,AuthenticatesAndRegistersUsers, ThrottlesLogins;

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
        return view('auth.register-user');
    }
    /**
     *
     */
    public function setPassword(){
       $user =  session('user');
        if(empty($user)){
            return redirect('auth.register-user');
        }
        return view('auth.register-password',['user'=>$user]);
    }
    /**
     * 添加商铺信息
     */
    public function addShop(){
        $user =  session('user');
        if(empty($user)){
            return redirect('auth.register-user');
        }
        return view('auth.register-user-shop',['user'=>$user]);
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
        $user =  session('user');
        session()->forget('user');
        return view('auth.reg-success',['user_name'=>$user['user_name'] ]);
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
