<?php
/**
 * User deliveries handler
 */

namespace App\Http\Support\Checkout;


use App\Models\City;
use App\Models\DeliveryType;
use App\Models\PostService;
use App\Models\User;
use App\Models\UserDelivery;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

class UserDeliveryRepository
{
    /**
     * @var UserDelivery
     */
    private $userDelivery;

    /**
     * @var City
     */
    private $city;

    /**
     * @var PostService
     */
    private $postService;

    /**
     * @var DeliveryType
     */
    private $deliveryType;

    /**
     * UserDeliveryRepository constructor.
     * @param UserDelivery $userDelivery
     * @param City $city
     * @param PostService $postService
     * @param DeliveryType $deliveryType
     */
    public function __construct(UserDelivery $userDelivery, City $city, PostService $postService, DeliveryType $deliveryType)
    {
        $this->userDelivery = $userDelivery;
        $this->city = $city;
        $this->postService = $postService;
        $this->deliveryType = $deliveryType;
    }

    public function getDeliveryTypes()
    {
        return $this->deliveryType->all()->pluck('title', 'id')->toArray();
    }

    /**
     * Get cities that have storage.
     *
     * @return array
     */
    public function getCitiesHaveStorage()
    {
        return $this->city->has('storage')->distinct()->get()->pluck('title', 'id')->toArray();
    }

    /**
     * Det Post services data.
     *
     * @return array
     */
    public function getPostServices()
    {
        return $this->postService->all()->pluck('title', 'id')->toArray();
    }

    /**
     * Get last user delivery.
     *
     * @param User|Authenticatable $user
     * @return UserDelivery|null
     */
    public function getLastUserDelivery($user)
    {
        $userId = $user->id;

        return $this->userDelivery
            ->whereHas('userInvoice', function ($query) use ($userId) {
                $query->where('users_id', $userId);
            })
            ->join('user_invoices', 'user_invoices.user_deliveries_id', '=', 'user_deliveries.id')
            ->orderByDesc('user_invoices.created_at')
            ->first();
    }

    /**
     * Create user delivery.
     *
     * @param array $deliveryData
     * @return UserDelivery
     */
    public function createUserDelivery(array $deliveryData)
    {
        if (isset($deliveryData['name'])){
            $deliveryData['name'] = Str::ucfirst($deliveryData['name']);
        }

        return $this->userDelivery->create($deliveryData);
    }
}