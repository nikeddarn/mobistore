<?php

namespace App\Http\Controllers\Warranty;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WarrantyController extends Controller
{
    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showUserWarranties(User $user){
        return view('content.user.warranty.show.index');
    }
}
