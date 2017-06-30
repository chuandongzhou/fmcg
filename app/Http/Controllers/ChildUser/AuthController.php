<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/10/20
 * Time: 14:49
 */
namespace App\Http\Controllers\ChildUser;


class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('child.guest', ['except' => 'getLogout']);
    }

    /**
     * 后台登录页
     *
     * @return \Illuminate\View\View
     */
    public function getLogin()
    {
        return view('child-user.auth.login');
    }


    /**
     * 退出登录
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getLogout()
    {
        child_auth()->logout();
        return redirect(url('child-user/auth/login'));
    }
}