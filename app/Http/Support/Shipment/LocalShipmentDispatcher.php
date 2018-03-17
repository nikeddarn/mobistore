<?php
/**
 * Local shipment dispatcher.
 */

namespace App\Http\Support\Shipment;

use App\Models\Shipment;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class LocalShipmentDispatcher extends ShipmentDispatcher
{
    /**
     * Get nearest not dispatched shipment or create new shipment.
     *
     * @return Shipment|\Illuminate\Database\Eloquent\Model
     * @throws Exception
     */
    public function getOrCreateNextShipment()
    {
        try{
            $this->databaseManager->beginTransaction();

            $shipment = static ::buildRetrieveNextShipmentQuery()->first();

            $this->databaseManager->commit();

            return $shipment ? $shipment : static::buildNextShipment();
        }catch (Exception $exception){
            $this->databaseManager->rollBack();

            throw new Exception($exception->getMessage());
        }
    }

    /**
     * Create next shipment. Return shipment date.
     *
     * @return Shipment|\Illuminate\Database\Eloquent\Model
     * @throws Exception
     */
    public function createNextShipment()
    {
        try{
            $this->databaseManager->beginTransaction();

            $shipment = static::buildNextShipment();

            $this->databaseManager->commit();

            return $shipment;
        }catch (Exception $exception){
            $this->databaseManager->rollBack();

            throw new Exception($exception->getMessage());
        }
    }

    /**
     * Build retrieve query of nearest possibly shipment arrival.
     *
     * @return Builder
     */
    protected function buildRetrieveNextShipmentQuery(): Builder
    {
        return parent::buildRetrieveNextShipmentQuery()->has('localShipment');
    }

    /**
     * Build new shipment.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function buildNextShipment()
    {
        $shipment = parent::buildNextShipment();
        $localShipment = $shipment->localShipment()->create([]);
        $shipment->setRelation('localShipment', $localShipment);
        return $shipment;
    }

    /**
     * Define next possibly shipment departure.
     *
     * @return Carbon
     */
    protected function defineDepartureDate(): Carbon
    {
        $possibleDay = Carbon::now();

        // add 1 day if current time more than max departure time
        if ($possibleDay > Carbon::today()->addHours(config('shop.shipment.current_day_delivery_max_time.local'))) {
            $possibleDay->addDay();
        }

        // add days till departure day is weekday
        while ($possibleDay->isWeekend()) {
            $possibleDay->addDay();
        }

        return $possibleDay;
    }


    /**
     * Define next possibly shipment arrival.
     *
     * @param Carbon $departureDay
     * @return Carbon
     */
    protected function defineArrivalDate(Carbon $departureDay = null): Carbon
    {
        if (!$departureDay){
            $departureDay = self::defineDepartureDate();
        }

        return $departureDay;
    }
}