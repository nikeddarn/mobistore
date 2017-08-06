<?php

namespace App\Http\Controllers\Order;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showOrders(User $user){
        return view('content.user.order.show.index');
    }
}
