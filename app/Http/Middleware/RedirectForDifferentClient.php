<?php

namespace App\Http\Middleware;

use Closure;

class RedirectForDifferentClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $terTypes = cons('ter_types');

        $inWindows = in_windows();
        if ($request->input('ter_type', head($terTypes)) === head($terTypes)) {
            $mobileUrl = 'http://m.dingbaida.com';

            if ($inWindows && !$request->is('child-user/*', 'admin/*', 'upload/file/*') && false !== strpos($request->root(), $mobileUrl)) {
                    return redirect('http://dingbaida.com');
            } else if (!$inWindows && false === strpos($request->root(), $mobileUrl)) {
                return redirect($mobileUrl);
            }
        }

        return $next($request);
    }
}
