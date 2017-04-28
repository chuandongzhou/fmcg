<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class VerifyDeposit
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
     * @param  string $validateExpire
     * @return mixed
     */
    public function handle($request, Closure $next, $validateExpire = '')
    {
        /*$userType = $this->auth->user()->type;
        $userTypes = cons('user.type');
        $user = $this->auth->user();
        if (in_array($userType, [$userTypes['wholesaler'], $userTypes['supplier']])) {
            $condition = $validateExpire ? !$user->deposit : !$user->deposit || $user->is_expire;
            if ($condition) {
                if ($request->ajax()) {
                    return response('Unauthorized.', 401);
                } else {
                    abort(404);
                }
            }
        }*/

        return $next($request);
    }
}
