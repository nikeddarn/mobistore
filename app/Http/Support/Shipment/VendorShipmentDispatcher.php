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
     * Get possible arrival date.
     *
     * @param array $invoiceVendors
     * @return Carbon|null
     */
    public function calculateDeliveryDay(array $invoiceVendors)
    {
        // get max of shipment arrivals by vendors
        $vendorShipments = $this->buildRetrieveNextShipmentQuery()->whereHas('vendorShipment', function ($query) use ($invoiceVendors) {
            $query->whereIn('vendors_id', $invoiceVendors);
        })
            ->selectRaw('MAX(shipments.planned_arrival) AS planned_arrival')
            ->groupBy('vendor_shipments.vendors_id')
            ->get();

        if ($vendorShipments->count() === count($invoiceVendors)) {
            // all vendors have unclosed shipment
            return $vendorShipments->max('planned_arrival');
        } else {
            // define nearest departure days from vendor shipment schedule
            $possibleDepartureDate = $this->workCalendar->getNearestVendorWeekDay(config('shop.shipment.current_day_delivery_max_time.vendor'));
            // define vendor possible shipments
            $vendorPossibleShipments = $this->vendorShipmentSchedule
                ->selectRaw('MIN(planned_departure) AS planned_departure')
                ->groupBy('vendors_id')
                ->where('planned_departure', '>=', $possibleDepartureDate->toDateString())
                ->whereIn('vendors_id', $invoiceVendors)
                ->get();

            if ($vendorPossibleShipments->count() === count($invoiceVendors)) {
                // all vendors shipments are planned
                $plannedDeparture = $vendorPossibleShipments->max('planned_departure');
                // add 1 day for delivery
                $plannedArrival = Carbon::createFromTimestamp($plannedDeparture)->addDays(1);
                return $plannedArrival;
            } else {
                // arrival date will be defined later
                return null;
            }
        }
    }

    /**
     * Get nearest not dispatched shipment or create new shipment.
     *
     * @param int $vendorId
     * @return Shipment|\Illuminate\Database\Eloquent\Model
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
}