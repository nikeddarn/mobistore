<?php
/**
 * Shipment dispatcher
 */

namespace App\Http\Support\Shipment;


use App\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;

class ShipmentDispatcher
{
    /**
     * @var Shipment
     */
    private $shipment;

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
     * Get possible arrival date.
     *
     * @return Carbon
     */
    public function getPossibleNearestShipmentArrival():Carbon
    {
        return static::defineArrivalDate();
    }

    /**
     * Get nearest not dispatched shipment.
     *
     * @return Carbon|Builder|\Illuminate\Database\Eloquent\Model|null
     */
    public function getNextShipment()
    {
        return static::buildRetrieveNextShipmentQuery()->first();
    }

    /**
     * Build retrieve query of nearest possibly shipment arrival.
     *
     * @return Builder
     */
    protected function buildRetrieveNextShipmentQuery()
    {
        return $this->createRetrieveQuery()->whereNull('dispatched')->orderByDesc('created_at');
    }

    /**
     * Build new shipment.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function buildNextShipment()
    {
        $departureDay = static::defineDepartureDate();

        return $this->shipment->create([
            'planned_departure' => $departureDay->toDateString(),
            'planned_arrival' => static::defineArrivalDate($departureDay)->toDateString(),
        ]);
    }

    /**
     * Create query builder.
     *
     * @return Builder
     */
    private function createRetrieveQuery():Builder
    {
        return $this->shipment->select();
    }
}