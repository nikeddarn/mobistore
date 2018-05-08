<?php
/**
 * Shipment dispatcher
 */

namespace App\Http\Support\Shipment;


use App\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;

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
     * @var ShipmentCalendar
     */
    protected $workCalendar;

    /**
     * ShipmentDispatcher constructor.
     *
     * @param Shipment $shipment
     * @param DatabaseManager $databaseManager
     * @param ShipmentCalendar $workCalendar
     */
    public function __construct(Shipment $shipment, DatabaseManager $databaseManager, ShipmentCalendar $workCalendar)
    {
        $this->shipment = $shipment;
        $this->databaseManager = $databaseManager;
        $this->workCalendar = $workCalendar;
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
     * @param int $courierId
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function createShipment(Carbon $departure, Carbon $arrival, int $courierId)
    {
        return $this->shipment->create([
            'planned_departure' => $departure,
            'planned_arrival' => $arrival,
            'couriers_id' => $courierId,
        ]);
    }
}