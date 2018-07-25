<?php

namespace App\Http\Middleware\Auth;

use App\Contracts\Shop\Roles\UserRolesInterface;
use Closure;

class StorageManagerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!auth('web')->check()){
            return redirect()->guest(route('admin.login'));
        }

        $user = auth('web')->user();

        // user is own storage manager
        if ($user->role()->where('id', UserRolesInterface::STOREKEEPER)->count()){
            return $next($request);
        }

        return abort(403, 'Forbidden');
    }
}
