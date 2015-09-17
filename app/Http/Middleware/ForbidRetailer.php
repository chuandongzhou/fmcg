<?php

namespace App\Http\Middleware;

use Closure;

class ForbidRetailer
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
        if (auth()->user()->type == cons('user.type.retailer')) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return back()->withInput();
            }
        }

        return $next($request);
    }
}
