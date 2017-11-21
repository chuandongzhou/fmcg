<?php

namespace App\Http\Middleware;

use Closure;
use WeiHeng\WarehouseKeeper\WarehouseKeeperGuard;

class WarehouseKeeperAuthenticate
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
    public function __construct(WarehouseKeeperGuard $auth)
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
        if ($this->auth->guest()) {
            return response('Unauthorized.', 401);
        }
        return $next($request);
    }
}
