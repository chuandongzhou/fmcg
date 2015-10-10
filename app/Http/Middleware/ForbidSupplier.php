<?php

namespace App\Http\Middleware;

use Closure;

class ForbidSupplier
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
        if (auth()->user()->type > cons('user.type.wholesaler')) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->back();
            }
        }

        return $next($request);
    }
}
