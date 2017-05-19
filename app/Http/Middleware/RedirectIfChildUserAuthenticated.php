<?php

namespace App\Http\Middleware;

use Closure;
use WeiHeng\ChildUser\Guard;

class RedirectIfChildUserAuthenticated
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
     * @param  Guard $auth
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
        if ($this->auth->check()) {
            $indexNode = $this->auth->first_node;
            return redirect(url($indexNode->url));
        }

        return $next($request);
    }
}
