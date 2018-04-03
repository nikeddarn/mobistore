<?php
/**
 * Post shipment dispatcher.
 */

namespace App\Http\Support\Shipment;

use App\Models\Shipment;
use Carbon\Carbon;
use Exception;

class PostShipmentDispatcher extends ShipmentDispatcher
{
    /**
     * Get nearest not dispatched shipment or create new shipment of given post service.
     *
     * @param int $postServiceId
     * @return Shipment|\Illuminate\Database\Eloquent\Model
     * @throws Exception
     */
    public function getNextShipment(int $postServiceId)
    {
        return $this->buildRetrieveNextShipmentQuery()->whereHas('postShipment', function ($query) use ($postServiceId) {
            $query->where('post_services_id', $postServiceId);
        })->first();
    }

    /**
     * Create next shipment. Return shipment date.
     *
     * @param int $postServiceId
     * @param Carbon $departure
     * @param Carbon $arrival
     * @param int $courierId
     * @return Shipment|\Illuminate\Database\Eloquent\Model
     * @throws Exception
     */
    public function createNextShipment(int $postServiceId, Carbon $departure, Carbon $arrival, int $courierId)
    {
        try {
            $this->databaseManager->beginTransaction();

            $shipment = $this->createShipment($departure, $arrival, $courierId);

            $postShipment = $shipment->postShipment()->create([
                'post_services_id' => $postServiceId,
            ]);
            $shipment->setRelation('postShipment', $postShipment);

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
        return $this->workCalendar->getNearestLocalWeekDay(config('shop.shipment.current_day_delivery_max_time.post'));
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