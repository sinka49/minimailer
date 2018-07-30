<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Auth;

class AuthenticateBackend
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
        if (Auth::guard($guard)->guest() || !Auth::isCurrentUserAdmin()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }
            else {

                $str = explode("/", $request->path());
                $new = $str[0]."/".$str[1];

                if( !in_array($new, ['opera/login', 'opera/register', 'opera/password']) ){

                    return redirect()->guest('opera/login');
                }
            }
        }
        return $next($request);
    }
}
