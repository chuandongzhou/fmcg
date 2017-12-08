<?php

namespace App\Http\Middleware;

use Closure;
use WeiHeng\Responses\Apiv1Response;
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
     * MakerSalesmanAuthenticate constructor.
     *
     * @param \WeiHeng\Salesman\Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * handle
     *
     * @param $request
     * @param \Closure $next
     * @return static
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest() || is_null($this->auth->user()->maker_id ?? null)) {
            return Apiv1Response::create('forbidden', []);
        }
        return $next($request);
    }
}
