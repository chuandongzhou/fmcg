<?php

namespace App\Http\Middleware;

use Closure;
use WeiHeng\Salesman\Guard;

class SalesmanAuthenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param \WeiHeng\Salesman\Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!in_windows() && auth()->guest()) {
            if ($this->auth->guest()) {
                return response('Unauthorized.', 401);
            } else {
                if ($this->auth->user()->status == cons('status.off')) {
                    return response('suspended', 403);
                }
            }
        }

        return $next($request);
    }
}
