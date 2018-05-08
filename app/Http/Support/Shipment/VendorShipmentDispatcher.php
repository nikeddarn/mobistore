<?php
/**
 * Vendor shipment dispatcher.
 */

namespace App\Http\Support\Shipment;


use App\Models\VendorShipmentSchedule;
use App\Models\Shipment;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;

class VendorShipmentDispatcher extends ShipmentDispatcher
{
    /**
     * @var VendorShipmentSchedule
     */
    private $vendorShipmentSchedule;

    /**
     * VendorShipmentDispatcher constructor.
     *
     * @param Shipment $shipment
     * @param DatabaseManager $databaseManager
     * @param VendorShipmentSchedule $vendorShipmentSchedule
     * @param ShipmentCalendar $workCalendar
     */
    public function __construct(Shipment $shipment, DatabaseManager $databaseManager, VendorShipmentSchedule $vendorShipmentSchedule, ShipmentCalendar $workCalendar)
    {
        parent::__construct($shipment, $databaseManager, $workCalendar);
        $this->vendorShipmentSchedule = $vendorShipmentSchedule;
    }

    /**
     * Get possible arrival date by vendors.
     *
     * @param array $invoiceVendors
     * @return Carbon|null
     */
    public function calculateDeliveryDayByVendors(array $invoiceVendors)
    {
        // get max of all vendor shipment arrivals by vendors
        $maxVendorShipmentsArrival = $this->getMaxArrivalDateFromVendorsShipments($invoiceVendors);
        if ($maxVendorShipmentsArrival) {
            return Carbon::createFromFormat('Y-m-d h:i:s', $maxVendorShipmentsArrival->planned_arrival);
        }

        // get max arrival dates of all vendors by their schedules
        $maxVendorScheduleArrival = $this->getMaxArrivalDateFromVendorsSchedule($invoiceVendors);
        if ($maxVendorScheduleArrival) {
            return Carbon::createFromFormat('Y-m-d h:i:s', $maxVendorScheduleArrival->planned_arrival);
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
            return Carbon::createFromFormat('Y-m-d h:i:s', $maxVendorInvoicesShipmentArrival->planned_arrival);
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
        })->first();
    }

    /**
     * Create next shipment. Return shipment date.
     *
     * @param int $vendorId
     * @param Carbon $departure
     * @param Carbon $arrival
     * @param int $courierId
     * @return Shipment|\Illuminate\Database\Eloquent\Model
     * @throws Exception
     */
    public function createNextShipment(int $vendorId, Carbon $departure, Carbon $arrival, int $courierId)
    {
        try {
            $this->databaseManager->beginTransaction();

            $shipment = $this->createShipment($departure, $arrival, $courierId);

            $vendorShipment = $shipment->vendorShipment()->create([
                'vendors_id' => $vendorId,
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
     * Define next possibly shipment departure.
     *
     * @param int $vendorId
     * @return Carbon|null
     */
    protected function defineDepartureDate(int $vendorId)
    {
        return $this->workCalendar->getNextPlannedVendorDepartureDay($vendorId);
    }


    /**
     * Define next possibly shipment arrival.
     *
     * @param int $vendorId
     * @param Carbon $departureDay
     * @return Carbon|null
     */
    protected function defineArrivalDate(int $vendorId, Carbon $departureDay = null)
    {
        if (!$departureDay) {
            $departureDay = $this->defineDepartureDate($vendorId);
        }

        if ($departureDay) {
            // add 1 day for delivery
            $arrivalDay = (clone $departureDay)->addDay();
        } else {
            $arrivalDay = null;
        }

        return $arrivalDay;
    }

    /**
     * Get max of arrival date from not dispatched vendor shipments. All shipments are required.
     *
     * @param array $invoiceVendors
     *
     * @return Model|null
     */
    private function getMaxArrivalDateFromVendorsShipments(array $invoiceVendors)
    {
        return $this->buildRetrieveNextShipmentQuery()
//            ->join('vendor_shipments', 'vendor_shipments.shipments_id', '=', 'shipments.id')
//            ->whereHas('vendorShipment', function ($query) use ($invoiceVendors){
//                $query->whereIn('vendors_id', $invoiceVendors);
//            })
            ->whereHas('vendorShipment', function ($query) use ($invoiceVendors) {
                $query->whereIn('vendors_id', $invoiceVendors);
            }, '=', count($invoiceVendors))
            ->max('planned_arrival');
//            ->first();
    }

    /**
     * Get max of all vendors possible shipments departure date. All shipments are required.
     *
     * @param array $invoiceVendors
     * @return Model|null
     */
    private function getMaxArrivalDateFromVendorsSchedule(array $invoiceVendors)
    {
        // define nearest departure days from vendor shipment schedule
        $possibleDepartureDate = $this->workCalendar->getNearestVendorWeekDay(config('shop.shipment.current_day_delivery_max_time.vendor'));

        // define max of all vendors possible shipments departure date
        return $this->vendorShipmentSchedule
            ->where('planned_departure', '>=', $possibleDepartureDate->toDateString())
//            ->whereIn('vendors_id', $invoiceVendors)
            ->whereHas('vendor', function ($query) use ($invoiceVendors) {
                $query->whereIn('id', $invoiceVendors);
            }, '=', count($invoiceVendors))
            ->max('planned_arrival');
//            ->first();
    }

    /**
     * Get max planned arrival of all vendor invoice shipments.
     *
     * @param array $invoicesId
     * @return Model|null
     */
    private function getMaxArrivalDateFromInvoices(array $invoicesId)
    {
        return $this->shipment->whereHas('invoice', function ($query) use ($invoicesId) {
            $query->whereIn('id', $invoicesId);
        }, '=', count($invoicesId))
            ->max('planned_arrival');
    }
}