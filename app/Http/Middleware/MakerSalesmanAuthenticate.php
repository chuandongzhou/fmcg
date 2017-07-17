<?php

namespace App\Http\Middleware;

use Closure;
use WeiHeng\Salesman\Guard;

class MakerSalesmanAuthenticate
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
     * @param \WeiHeng\ChildUser\Guard $auth
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
        if (is_null($this->auth->user()->maker)) {
            return $this->error('身份错误!');
        }
        return $next($request);
    }
}
