<?php
/**
 * Vendor shipment dispatcher.
 */

namespace App\Http\Support\Shipment;


use App\Http\Support\Shipment\Calendar\VendorShipmentCalendar;
use App\Models\Shipment;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

class VendorShipmentDispatcher extends ShipmentDispatcher
{
    /**
     * @var VendorShipmentCalendar
     */
    private $shipmentCalendar;

    /**
     * VendorShipmentDispatcher constructor.
     *
     * @param Shipment $shipment
     * @param DatabaseManager $databaseManager
     * @param VendorShipmentCalendar $shipmentCalendar
     */
    public function __construct(Shipment $shipment, DatabaseManager $databaseManager, VendorShipmentCalendar $shipmentCalendar)
    {
        parent::__construct($shipment, $databaseManager);
        $this->shipmentCalendar = $shipmentCalendar;
    }

    /**
     * Get not dispatched or not received vendor shipments with invoices and courier.
     *
     * @param int $vendorId
     * @return Collection
     */
    public function getNotCompletedVendorShipments(int $vendorId): Collection
    {
        return $this->buildRetrieveNextShipmentQuery()
            ->orWhereNull('received')
            ->whereHas('vendorShipment', function ($query) use ($vendorId) {
                $query->where('vendors_id', $vendorId);
            })
            ->orderBy('planned_departure')
            ->with('vendorShipment.vendorCourier', 'invoice.invoiceType')
            ->get();
    }

    /**
     * Get not dispatched vendor shipments that is uo to date.
     *
     * @param int $vendorId
     * @return Collection
     */
    public function getAvailableVendorShipments(int $vendorId): Collection
    {
        return $this->buildRetrieveNextShipmentQuery()
            ->leftJoin('invoices', 'invoices.shipments_id', '=', 'shipments.id')
            ->join('vendor_shipments', function ($join) use ($vendorId) {
                $join->on('vendor_shipments.shipments_id', '=', 'shipments.id')->where('vendors_id', $vendorId);
            })
            ->join('vendor_couriers', 'vendor_couriers.id', '=', 'vendor_shipments.vendor_couriers_id')
            ->selectRaw('IFNULL(SUM(invoices.invoice_sum), 0) AS shipment_sum, shipments.id, shipments.planned_departure, vendor_couriers.name')
            ->whereDate('planned_departure', '>=', Carbon::now()->toDateString())
            ->orderBy('planned_departure')
            ->groupBy('shipments.id')
            ->get();
    }

    /**
     * Get possible arrival date by vendors from shipments or courier schedules.
     *
     * @param array $invoiceVendors
     * @return Carbon|null
     */
    public function calculateDeliveryDayByVendorShipmentsOrSchedules(array $invoiceVendors)
    {
        // get max of all vendor shipment arrivals by vendors
        $maxVendorShipmentsArrival = $this->getMaxArrivalDateFromVendorsShipments($invoiceVendors);

        if ($maxVendorShipmentsArrival) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $maxVendorShipmentsArrival);
        }

        // get max arrival dates of all vendors by their schedules
        $maxVendorScheduleArrival = $this->shipmentCalendar->getMaxArrivalDateFromVendorsSchedule($invoiceVendors);

        if ($maxVendorScheduleArrival) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $maxVendorScheduleArrival);
        }

        // arrival date will be defined later
        return null;
    }

    /**
     * Get possible arrival date by vendors from shipments or courier schedules.
     *
     * @param array $invoiceVendors
     * @return Carbon|null
     */
    public function calculateDeliveryDayByVendorShipments(array $invoiceVendors)
    {
        // get max of all vendor shipment arrivals by vendors
        $maxVendorShipmentsArrival = $this->getMaxArrivalDateFromVendorsShipments($invoiceVendors);

        if ($maxVendorShipmentsArrival) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $maxVendorShipmentsArrival);
        }

        // arrival date will be defined later
        return null;
    }


    /**
     * Get max planned arrival as Carbon of all vendor invoice shipments.
     *
     * @param array $invoicesId
     * @return Carbon|null
     */
    public function calculateDeliveryDateByInvoices(array $invoicesId)
    {
        $maxVendorInvoicesShipmentArrival = $this->getMaxArrivalDateFromInvoices($invoicesId);
        if ($maxVendorInvoicesShipmentArrival) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $maxVendorInvoicesShipmentArrival);
        }

        return null;
    }

    /**
     * Get nearest not dispatched shipment or create new shipment.
     *
     * @param int $vendorId
     * @return Shipment|\Illuminate\Database\Eloquent\Model|null
     */
    public function getNextShipment(int $vendorId)
    {
        return $this->buildRetrieveNextShipmentQuery()->whereHas('vendorShipment', function ($query) use ($vendorId) {
            $query->where('vendors_id', $vendorId);
        })
            ->orderBy('planned_departure')
            ->whereDate('planned_departure', '>=', Carbon::now()->toDateString())
            ->first();
    }

    /**
     * Create vendor shipment.
     *
     * @param int $vendorId
     * @param Carbon $departure
     * @param Carbon $arrival
     * @param int $courierId
     * @return Shipment|\Illuminate\Database\Eloquent\Model
     * @throws Exception
     */
    public function createVendorShipment(Carbon $departure, Carbon $arrival, int $vendorId, int $courierId)
    {
        try {
            $this->databaseManager->beginTransaction();

            $shipment = $this->createShipment($departure, $arrival);

            $vendorShipment = $shipment->vendorShipment()->create([
                'vendors_id' => $vendorId,
                'vendor_couriers_id' => $courierId,
            ]);
            $shipment->setRelation('vendorShipment', $vendorShipment);

            $this->databaseManager->commit();

            return $shipment;
        } catch (Exception $exception) {
            $this->databaseManager->rollBack();

            throw new Exception($exception->getMessage());
        }
    }

    /**
     * Is vendor shipment exist ?
     *
     * @param int $vendorCourierId
     * @param Carbon $departure
     * @return bool
     */
    public function vendorShipmentExists(int $vendorCourierId, Carbon $departure)
    {
        return (bool)$this->shipment->whereDate('planned_departure', $departure->toDateString())
            ->whereHas('vendorShipment', function ($query) use ($vendorCourierId) {
                $query->where('vendor_couriers_id', $vendorCourierId);
            })
            ->count();
    }

    /**
     * Get max of arrival date from not dispatched vendor shipments. All shipments are required.
     *
     * @param array $vendorsId
     *
     * @return string|null
     */
    private function getMaxArrivalDateFromVendorsShipments(array $vendorsId)
    {
        return $this->buildRetrieveNextShipmentQuery()
            ->join('vendor_shipments', function ($join) use ($vendorsId) {
                $join->on('vendor_shipments.shipments_id', '=', 'shipments.id')->whereIn('vendors_id', $vendorsId);
            })
            ->selectRaw('MIN(shipments.planned_arrival) AS arrival')
            ->groupBy('vendor_shipments.vendors_id')
            ->havingRaw('COUNT( DISTINCT arrival) = ' . count($vendorsId))
            ->get()
            ->max('arrival');
    }

    /**
     * Get max planned arrival of all vendor invoice shipments.
     *
     * @param array $invoicesId
     * @return string|null
     */
    private function getMaxArrivalDateFromInvoices(array $invoicesId)
    {
        return $this->shipment->whereHas('invoice', function ($query) use ($invoicesId) {
            $query->whereIn('id', $invoicesId);
        }, '=', count($invoicesId))
            ->get()
            ->max('planned_arrival');
    }
}