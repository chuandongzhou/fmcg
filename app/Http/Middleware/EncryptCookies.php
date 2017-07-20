<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Illuminate\Cookie\Middleware\EncryptCookies as BaseEncrypter;
use Illuminate\Http\Request;
use Closure;

class EncryptCookies extends BaseEncrypter
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        'province_id',
        'city_id'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request = $this->_decrypt($request);
        // 是否在浏览器
        $inWindows = in_windows();
        $terTypes = cons('ter_types');
        if (!$request->ajax() && $request->input('ter_type', head($terTypes)) === head($terTypes)) {
            $mobileUrl = 'http://m.dingbaida.com';
            if ($inWindows && !$request->is('child-user/*', 'admin/*', 'upload/file/*')) {
                if (false !== strpos($request->root(), $mobileUrl)) {
                    return redirect('http://dingbaida.com');
                }
                if (!$request->is('auth/*')) {
                    $cookieJar = app('cookie');
                    $nowTimestamp = Carbon::now()->timestamp;
                    $expire = Carbon::now()->addDays(30)->diffInMinutes();
                    if ($lastHandleTime = $request->cookie('last_handle_time')) {
                        $diffInMinutes = Carbon::now()->diffInMinutes(Carbon::createFromTimestamp($lastHandleTime));
                        if ($diffInMinutes >= 60) {
                            //超出30分钟未操作退出登录
                            return redirect(url('auth/logout'));
                        }

                    }
                    $cookieJar->queue('last_handle_time', $nowTimestamp, $expire);
                }
            } else if (!$inWindows) {
                if (false === strpos($request->root(), $mobileUrl)) {
                    return redirect($mobileUrl);
                }
            }
        }

        $response = $next($request);
        return $this->encrypt($response);
    }


    /**
     * Decrypt the cookies on the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Request
     */
    protected function _decrypt(Request $request)
    {
        foreach ($request->cookies as $key => $c) {
            if ($this->isDisabled($key)) {
                continue;
            }

            try {
                $request->cookies->set($key, $this->decryptCookie($c));
            } catch (\Exception $e) {
                $request->cookies->set($key, null);
            }
        }

        return $request;
    }
}
