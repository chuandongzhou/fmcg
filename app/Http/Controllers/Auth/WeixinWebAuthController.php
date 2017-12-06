<?php

namespace App\Http\Controllers\Auth;


class WeixinWebAuthController extends SocialiteController
{

    protected $driver = 'weixinweb';


    /**
     * 绑定平台账号
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function bindSocialite()
    {
        $token = $this->getToken(session('socialite_token'));
        if (!array_get($token, 'token')) {
            return redirect('auth/login');
        }
        return view('auth.register-bind-socialite', compact('token'));
    }

    /**
     * 处理返回
     *
     * @param $user
     * @param null $error
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\Response
     */
    public function handleLoginResponse($user, $error = null)
    {
        if ($error) {
            dd($error);
        } else {
            $redirectUrl = $user->type <= cons('user.type.wholesaler') ? '/' : url('personal/info');
            return redirect($redirectUrl);
        }
    }

}
