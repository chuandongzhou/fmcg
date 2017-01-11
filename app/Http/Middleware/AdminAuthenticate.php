<?php

namespace App\Http\Middleware;

use Closure;
use WeiHeng\Admin\Guard;

class AdminAuthenticate
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
     * @param \WeiHeng\Admin\Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('admin/auth/login');
            }
        }else {
            $admin = admin_auth()->user();
            if ($request->method() == 'GET' && $admin->name != cons('admin.super_admin_name')) {
                $nodeUrls = $admin->node_urls;
                if (empty($nodeUrls) || !call_user_func_array('\Request::is', $nodeUrls)) {
                    return  redirect()->guest('admin');
                }
            }
        }

        return $next($request);
    }
}
