<?php

namespace App\Http\Middleware;

use Closure;
use WeiHeng\ChildUser\Guard;
use WeiHeng\Responses\Apiv1Response;

class ChildUserAuthenticate
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
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return new Apiv1Response('forbidden', ['message' => '没有权限']);
            }
            return redirect()->guest('child-user/auth/login');
        }

        $childUser = $this->auth->user();

        $nodeUrls = $childUser->nodes;

        $nodes = $nodeUrls->first(function ($key, $item) use ($request) {
            return $request->is($item->url) && $item->method == $request->method();
        });

        if (is_null($nodes)) {
            if ($request->ajax()) {
                return new Apiv1Response('forbidden', ['message' => '没有权限']);
            } else {
                $indexNode = $childUser->first_node;
                if ($indexNode) {
                    return redirect()->guest($indexNode->url);
                }
                return redirect(url('child-user/auth/logout'));
            }
        }

        return $next($request);
    }
}
