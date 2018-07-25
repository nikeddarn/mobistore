<?php
/**
 * Local shipment dispatcher.
 */

namespace App\Http\Support\Shipment;

use App\Http\Support\Shipment\Calendar\LocalShipmentCalendar;
use App\Models\Shipment;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\DatabaseManager;

class LocalShipmentDispatcher extends ShipmentDispatcher
{
    /**
     * @var LocalShipmentCalendar
     */
    private $shipmentCalendar;

    /**
     * LocalShipmentDispatcher constructor.
     *
     * @param Shipment $shipment
     * @param DatabaseManager $databaseManager
     * @param LocalShipmentCalendar $shipmentCalendar
     */
    public function __construct(Shipment $shipment, DatabaseManager $databaseManager, LocalShipmentCalendar $shipmentCalendar)
    {
        parent::__construct($shipment, $databaseManager);
        $this->shipmentCalendar = $shipmentCalendar;
    }

    /**
     * Get possible arrival date.
     *
     * @param array $invoiceStorages
     * @return Carbon
     */
    public function calculateDeliveryDay(array $invoiceStorages): Carbon
    {
        $nearestShipmentDay = $this->shipmentCalendar->getNearestShipmentDay(config('shop.shipment.current_day_delivery_max_time.local'));

        // add day for collect order on one storage if products are getting from several storages
        if (count($invoiceStorages) > 1) {
            $nearestShipmentDay->addDay();
        }

        // add days till shipment day is weekday
        while ($nearestShipmentDay->isWeekend()) {
            $nearestShipmentDay->addDay();
        }

        return $nearestShipmentDay;
    }

    /**
     * Get nearest not dispatched shipment.
     *
     * @param int $storageId
     * @return Shipment|\Illuminate\Database\Eloquent\Model
     */
    public function getNextShipment(int $storageId): Shipment
    {
        return $this->buildRetrieveNextShipmentQuery()->whereHas('localShipment', function ($query) use ($storageId) {
            $query->where('storages_id', $storageId);
        })->first();
    }

    /**
     * Create next shipment. Return shipment date.
     *
     * @param int $storageId
     * @param Carbon $departure
     * @param Carbon $arrival
     * @return Shipment
     * @throws Exception
     */
    public function createLocalShipment(Carbon $departure, Carbon $arrival, int $storageId): Shipment
    {
        try {
            $this->databaseManager->beginTransaction();

            $shipment = $this->createShipment($departure, $arrival);

            $localShipment = $shipment->localShipment()->create([
                'storages_id' => $storageId,
            ]);
            $shipment->setRelation('localShipment', $localShipment);

            $this->databaseManager->commit();

            return $shipment;
        } catch (Exception $exception) {
            $this->databaseManager->rollBack();

            throw new Exception($exception->getMessage());
        }
    }
}