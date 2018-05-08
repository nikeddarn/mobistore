<?php

namespace App\Http\Controllers\Vendor;

use App\Contracts\Shop\Roles\UserRolesInterface;
use App\Models\Vendor;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class VendorController extends Controller
{
    /**
     * @var Vendor
     */
    private $vendor;

    /**
     * @var Request
     */
    private $request;

    /**
     * VendorController constructor.
     *
     * @param Request $request
     * @param Vendor $vendor
     */
    public function __construct(Request $request, Vendor $vendor)
    {
        $this->vendor = $vendor;
        $this->request = $request;
    }

    /**
     * @return View|RedirectResponse
     */
    public function index()
    {
        $user = $this->request->user();

        $isVendorManager = in_array(UserRolesInterface::VENDOR_MANAGER, $user->role->pluck('id')->toArray());

        $vendorUser = $user->vendorUser()->where('users_id', $user->id)->first();

        if ($isVendorManager){
            return view('content.vendor.list.index')
                ->with('vendors', $this->getVendors());
        }

        if ($vendorUser){
            return redirect(route('vendor.account', ['vendorId' => $vendorUser->vendors_id]));
        }

        return abort(403);
    }

    /**
     * Get vendors with unclosed invoices.
     *
     * @return Collection
     */
    private function getVendors():Collection
    {
        return $this->vendor->with(['vendorInvoice' => function($query){
            $query->where('implemented', 0);
        }])->get();
    }
}
