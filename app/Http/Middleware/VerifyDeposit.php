<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use WeiHeng\Responses\Apiv1Response;

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
       /* $user = $this->auth->user();
        $userType = $user->type;
        $userTypes = cons('user.type');
        if (in_array($userType, [$userTypes['wholesaler'], $userTypes['supplier']])) {
            $condition = $validateExpire == 'true' ? !$user->deposit : !$user->deposit || $user->is_expire;
            if ($condition) {
                if ($request->ajax()) {
                    return new Apiv1Response('forbidden', ['message' => '没有权限']);
                } else {
                    abort(404);
                }
            }
        }*/

        return $next($request);
    }
}
