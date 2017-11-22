<?php

namespace App\Http\Controllers\Auth;

use App\Models\UserToken;
use Carbon\Carbon;
use Socialite;

class SocialiteController extends Controller
{

    protected $driver = 'weixinweb';

    protected $appDrivers = [
        'weixin'
    ];

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * 登录
     *
     * @return mixed
     */
    public function login()
    {
        return Socialite::with($this->driver)->redirect();
    }

    /**
     * 回调
     */
    public function callback()
    {
        $user = Socialite::driver($this->driver)->user();
        $token = array_get($user, 'unionid');
        $avatar = array_get($user, 'headimgurl');
        $name = array_get($user, 'nickname');

        $userToken = UserToken::ofType(cons('user.token.type.' . $this->driver))->where('token',
            $token)->with('user')->first();

        if ($userToken) {
            return $this->_handleLogin($userToken);
        } else {
            $tokenStr = $this->driver . '|' . $avatar . '|' . $name . '|' . $token;
            session(['socialite_token' => $tokenStr]);
            return redirect(url($this->driver . '-auth/bind-socialite'));
        }

    }

    /**
     * 绑定平台账号
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function bindSocialite()
    {
        $token = $this->_getToken(session('socialite_token'));
        if (!array_get($token, 'token')) {
            return redirect('auth/login');
        }
        return view('auth.register-bind-socialite', compact('token'));
    }


    /**
     * 处理登录
     *
     * @param $userToken
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function _handleLogin($userToken)
    {
        $request = app('request');
        $cookie = app('cookie');

        $user = $userToken->user;
        //是否已锁定
        if ($user->status != cons('status.on')) {
            return $this->_handleLoginResponce($user, '账号已锁定');
        }
        //帐号审核
        if ($user->audit_status != cons('user.audit_status.pass')) {
            return $this->_handleLoginResponce($user, '账户未审核或审核不通过');
        }

        //使用期限是否过期
        if ($user->is_expire) {
            return $this->_handleLoginResponce($user, '账户已到期，请续费');
        }

        if (!is_null($user->shop)) {
            $user->shop->address_name = $user->shop->address;
        }

        $nowTime = Carbon::now();

        if ($user->fill(['last_login_at' => $nowTime, 'last_login_ip' => $request->ip()])->save()) {
            auth()->login($user, true);
            $cookie->queue('login_error', null, -1);
            $userToken->increment('login_count');
            return $this->_handleLoginResponce($user);
        }
        return $this->_handleLoginResponce($user, '登录失败，请重试');
    }

    /**
     * 处理返回
     *
     * @param $user
     * @param null $error
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\Response
     */
    private function _handleLoginResponce($user, $error = null)
    {
        if (in_array($this->driver, $this->appDrivers)) {
            if ($error) {
                //return $this->error($error);
            } else {
                return $this->success(['user' => $user]);
            }
        } else {
            if ($error) {
                dd($error);
            } else {
                $redirectUrl = $user->type <= cons('user.type.wholesaler') ? '/' : url('personal/info');
                return redirect($redirectUrl);
            }
        }
    }

    /**
     * 获取token
     *
     * @param $token
     * @return array
     */
    private function _getToken($token)
    {
        list($type, $avatar, $nickname, $token) = explode('|', $token);

        return compact('type', 'avatar', 'nickname', 'token');
    }
}
