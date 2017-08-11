<?php

namespace App\Http\Controllers\Delivery;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DeliveryController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @internal param User $user
     */
    public function showUserDeliveries(Request $request){
        return view('content.user.delivery.show.index')->with($this->userProfileData($request->user()));
    }

    /**
     * Array of user profile data.
     *
     * @param User $user
     * @return array
     */
    private function userProfileData(User $user)
    {
        return [
            'name' => $user->name,
            'image' => Storage::url($user->image),
        ];
    }
}
