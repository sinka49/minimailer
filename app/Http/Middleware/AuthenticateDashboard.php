<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }
            else {

                $str = explode("/", $request->path());
                $new = $str[0]."/".$str[1];

                if( !in_array($new, ['dashboard/login', 'dashboard/register', 'dashboard/password']) ){

                    return redirect()->guest('dashboard/login');
                }
            }
        }
        return $next($request);
    }
}
