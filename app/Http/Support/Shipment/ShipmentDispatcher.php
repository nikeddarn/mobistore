<?php
/**
 * Shipment dispatcher
 */

namespace App\Http\Support\Shipment;


use App\Models\Shipment;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

abstract class ShipmentDispatcher
{
    /**
     * @var Shipment
     */
    protected $shipment;

    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * ShipmentDispatcher constructor.
     *
     * @param Shipment $shipment
     * @param DatabaseManager $databaseManager
     */
    public function __construct(Shipment $shipment, DatabaseManager $databaseManager)
    {
        $this->shipment = $shipment;
        $this->databaseManager = $databaseManager;
    }

    /**
     * Build retrieve query of nearest possibly shipment arrival.
     *
     * @return Builder
     */
    protected function buildRetrieveNextShipmentQuery()
    {
        return $this->shipment->whereNull('dispatched');
    }

    /**
     * Create new shipment.
     *
     * @param Carbon $departure
     * @param Carbon $arrival
     * @return Shipment
     */
    protected function createShipment(Carbon $departure, Carbon $arrival): Shipment
    {
        return $this->shipment->create([
            'planned_departure' => $departure,
            'planned_arrival' => $arrival,
        ]);
    }

    /**
     * Dispatch shipment by its id. Return dispatched invoices.
     *
     * @param int $shipmentId
     * @return Collection
     * @throws Exception
     */
    public function dispatchShipment(int $shipmentId): Collection
    {
        $shipment = $this->shipment->where('id', $shipmentId)->first();

        if (!$shipment) {
            throw new Exception('Deleting shipments id not found.');
        }

        $shipment->dispatched = Carbon::now()->toDateTimeString();
        $shipment->save();

        return $shipment->invoice()->with('vendorInvoice')->get();
    }

    /**
     * Delete shipment by its id. Return invoices that was unloaded.
     *
     * @param int $shipmentId
     * @return Collection
     * @throws Exception
     */
    public function deleteShipment(int $shipmentId): Collection
    {
        $shipment = $this->shipment->where('id', $shipmentId)->first();

        if (!$shipment) {
            throw new Exception('Deleting shipments id not found.');
        }

        $unloadedVendorInvoices = $shipment->invoice()->with('vendorInvoice')->get();

        $shipment->invoice()->update([
            'shipments_id' => null,
        ]);


        $shipment->delete();

        return $unloadedVendorInvoices;
    }
}