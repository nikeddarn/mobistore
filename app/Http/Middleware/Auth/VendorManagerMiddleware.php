<?php

namespace App\Http\Middleware\Auth;

use App\Contracts\Shop\Roles\UserRolesInterface;
use Closure;

class VendorManagerMiddleware
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

        // user is own vendor manager
        if ($user->role()->where('id', UserRolesInterface::VENDOR_MANAGER)->count()){
            return $next($request);
        }

        // route for concrete vendor
        if ($request->route()->hasParameter('vendorId')){

            // user is manager of vendor with route 'vendorId' parameter
            if ($user->vendorUser()->where('vendors_id', $request->route('vendorId'))->count()){
                return $next($request);
            }

        }else{
            // retrieve vendor user
            $vendorUser = $user->vendorUser()->first();

            // redirect to main page of vendor with retrieved id
            if ($vendorUser){
                return redirect(route('vendor.account', [
                    'vendorId' => $vendorUser->vendors_id,
                ]));
            }
        }

        return abort(403, 'Forbidden');
    }
}
