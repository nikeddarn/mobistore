<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{
    public function showMainPage(Request $request)
    {
        if ($this->guard()->check()) {
            return view('content.home.user.index')->with($this->getUserParameters($request));
        } else {
            return view('content.home.guest.index');
        }
    }

    private function guard()
    {
        return Auth::guard();
    }

    private function getUserParameters(Request $request)
    {
        return [
            'name' => $request->user()->name,
            'image' => Storage::url($request->user()->image ?? 'images/users/default.png'),
        ];
    }
}
