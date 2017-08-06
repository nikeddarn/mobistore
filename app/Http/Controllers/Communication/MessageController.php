<?php

namespace App\Http\Controllers\Communication;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showMessages(User $user)
    {
        return view('content.user.communication.messages.index');
    }
}
