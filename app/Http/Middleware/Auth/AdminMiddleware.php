<?php

namespace App\Http\Middleware\Auth;

use App\Contracts\Shop\Roles\UserRolesInterface;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if(!$user){
            return redirect()->guest(route('admin.login'));
        }
        if(!count(array_intersect($user->role->pluck('id')->toArray(), [UserRolesInterface::ROOT, UserRolesInterface::ADMIN]))){
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
