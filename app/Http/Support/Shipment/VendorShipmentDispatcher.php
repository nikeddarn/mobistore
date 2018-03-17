<?php
/**
 * Vendor shipment dispatcher.
 */

namespace App\Http\Support\Shipment;


use App\Models\CourierSchedule;
use App\Models\Shipment;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;

class VendorShipmentDispatcher extends ShipmentDispatcher
{
    /**
     * @var CourierSchedule
     */
    private $courierSchedule;

    /**
     * @var int
     */
    private $courierId;

    /**
     * VendorShipmentDispatcher constructor.
     *
     * @param Shipment $shipment
     * @param DatabaseManager $databaseManager
     * @param CourierSchedule $courierSchedule
     */
    public function __construct(Shipment $shipment, DatabaseManager $databaseManager, CourierSchedule $courierSchedule)
    {
        parent::__construct($shipment, $databaseManager);
        $this->courierSchedule = $courierSchedule;
    }

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
    protected function buildRetrieveNextShipmentQuery():Builder
    {
        return parent::buildRetrieveNextShipmentQuery()->has('vendorShipment');
    }

    /**
     * Build new shipment.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function buildNextShipment()
    {
        $shipment = parent::buildNextShipment();
        $vendorShipment = $shipment->vendorShipment()->create([
            'couriers_id' => $this->courierId,
        ]);
        $shipment->setRelation('vendorShipment', $vendorShipment);
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
        if ($possibleDay > Carbon::today()->addHours(config('shop.shipment.current_day_delivery_max_time.vendor'))){
            $possibleDay->addDay();
        }

        $possibleCourierDay = $this->courierSchedule->whereDay('planned_departure', '>=', $possibleDay->toDateString())->first();

        if ($possibleCourierDay){
            //set courier
            $this->courierId = $possibleCourierDay->couriers_id;

            // return date from schedule
            return $possibleCourierDay->planned_departure;
        }else{

            // return max pre order offer delivery date
            return Carbon::now()->addDays(config('shop.delivery.pre_order.max') - 1);
        }
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

        // add 1 day for delivery
        return (clone $departureDay)->addDay();
    }
}