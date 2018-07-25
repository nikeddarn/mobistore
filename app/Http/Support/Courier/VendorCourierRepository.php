<?php
/**
 * Vendor courier repository.
 */

namespace App\Http\Support\Courier;


use App\Models\VendorCourier;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class VendorCourierRepository
{
    /**
     * @var VendorCourier
     */
    private $vendorCourier;

    /**
     * VendorCourierRepository constructor.
     *
     * @param VendorCourier $vendorCourier
     */
    public function __construct(VendorCourier $vendorCourier)
    {
        $this->vendorCourier = $vendorCourier;
    }

    /**
     * Get all couriers of given vendor id.
     *
     * @param int $vendorId
     * @return Collection
     */
    public function getVendorCouriers(int $vendorId):Collection
    {
        return $this->vendorCourier->where('vendors_id', $vendorId)
            ->distinct()
            ->get();
    }

    /**
     * Create vendor courier.
     *
     * @param array $data
     * @return VendorCourier
     */
    public function createCourier(array $data): VendorCourier
    {
        $data['name'] = Str::ucfirst($data['name']);

        return $this->vendorCourier->create($data);
    }

    /**
     * Get courier with given id that belongs to given vendor.
     *
     * @param int $vendorId
     * @param int $courierId
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function getVendorCourierById(int $vendorId, int $courierId)
    {
        return $this->vendorCourier->where('vendors_id', $vendorId)
            ->where('id', $courierId)
            ->first();
    }
}