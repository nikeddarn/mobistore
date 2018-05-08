<?php
/**
 * Local shipment dispatcher.
 */

namespace App\Http\Support\Shipment;

use App\Models\Shipment;
use Carbon\Carbon;
use Exception;

class LocalShipmentDispatcher extends ShipmentDispatcher
{
    /**
     * Get possible arrival date.
     *
     * @param array $invoiceStorages
     * @return Carbon
     */
    public function calculateDeliveryDay(array $invoiceStorages): Carbon
    {
        $arrivalDay = $this->defineArrivalDate();

        // add day for collect order on one storage if products are getting from several storages
        if (count($invoiceStorages) > 1) {
            $arrivalDay->addDay();
        }

        return $arrivalDay;
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
     * @param int $courierId
     * @return Shipment|\Illuminate\Database\Eloquent\Model
     * @throws Exception
     */
    public function createNextShipment(int $storageId, Carbon $departure, Carbon $arrival, int $courierId): Shipment
    {
        try {
            $this->databaseManager->beginTransaction();

            $shipment = $this->createShipment($departure, $arrival, $courierId);

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

    /**
     * Define next possibly shipment departure.
     *
     * @return Carbon
     */
    public function defineDepartureDate(): Carbon
    {
        return $this->workCalendar->getNearestLocalWeekDay(config('shop.shipment.current_day_delivery_max_time.local'));
    }


    /**
     * Define next possibly shipment arrival.
     *
     * @param Carbon $departureDay
     * @return Carbon
     */
    public function defineArrivalDate(Carbon $departureDay = null): Carbon
    {
        if (!$departureDay) {
            $departureDay = $this->defineDepartureDate();
        }

        // arrival in same day
        return clone $departureDay;
    }
}