<?php

namespace App\Http\Middleware\Auth;

use App\Contracts\Shop\Roles\UserRolesInterface;
use App\Models\VendorUser;
use Closure;

class VendorUserMiddleware
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

        $user = $request->user();
        $vendorId = $request->route('vendorId');

        $isVendorManager = in_array(UserRolesInterface::VENDOR_MANAGER, $user->role->pluck('id')->toArray());
        $isVendorUser = $user->vendorUser()->where('users_id', $user->id)->where('vendors_id', $vendorId)->count();

        // user must be a vendor manager of store or manager of vendor
        if(!($isVendorManager || $isVendorUser)){
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
