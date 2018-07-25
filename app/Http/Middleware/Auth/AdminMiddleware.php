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
        if (!auth('web')->check()) {
            return redirect()->guest(route('admin.login'));
        }

        $adminRoles = [
            UserRolesInterface::ROOT,
            UserRolesInterface::ADMIN,
        ];

        // user has one of admin's role
        if (auth('web')->user()->role()->whereIn('id', $adminRoles)->count()) {
            return $next($request);
        }

        return abort(403, 'Forbidden');
    }
}
