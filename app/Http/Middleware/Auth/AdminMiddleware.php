<?php

namespace App\Http\Middleware\Auth;

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

        if(!$user->userRole->count()){
            abort(403, 'A user (Id=' . $user->id . ') without a role tried to enter the administrative section.');
        }

        return $next($request);
    }
}
