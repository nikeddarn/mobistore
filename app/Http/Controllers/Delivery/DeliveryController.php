<?php

namespace App\Http\Controllers\Delivery;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeliveryController extends Controller
{
    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showUserDeliveries(User $user){
        return view('content.user.delivery.show.index');
    }
}
