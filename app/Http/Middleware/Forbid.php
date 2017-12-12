<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Forbid
{

    /**
     * The Guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * Forbid constructor.
     *
     * @param \Illuminate\Contracts\Auth\Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param \Closure $next
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!$this->check($roles)) {
            return $request->ajax() ? response('Unauthorized.', 401) : redirect()->back();
        }
        return $next($request);
    }

    /**
     * check role
     *
     * @param $roles
     * @return bool
     */
    private function check($roles)
    {
        if (!count($roles)) {
            return false;
        }
        $userType = array_map(function ($role) {
            return cons('user.type.' . $role);
        }, $roles);
        if (in_array($this->auth->user()->type, $userType)) {
            return false;
        }
        return true;
    }
}