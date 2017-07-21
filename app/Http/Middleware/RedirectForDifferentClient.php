<?php

namespace App\Http\Middleware;

use Closure;

class RedirectForDifferentClient
{

    protected $except = [
        'api/v1/version',
        'upload/file/*'
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

        $isApp = $request->is('api/v1/*') && !$request->ajax();

        $inWindows = in_windows();
        if (!$isApp) {
            $mobileUrl = 'http://m.dingbaida.com';
            if ($inWindows && false !== strpos($request->root(), $mobileUrl)) {
                return redirect('http://dingbaida.com');
            } else if (!$inWindows && false === strpos($request->root(), $mobileUrl)) {
                return redirect($mobileUrl);
            }
        }

        return $next($request);
    }
}
