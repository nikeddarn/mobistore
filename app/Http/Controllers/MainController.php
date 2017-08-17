<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{
    /**
     * Return main view or home user view if authenticated.
     *
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showMainPage(Request $request)
    {
        if ($this->guard()->check()) {
            return view('content.user.home.index')->with($this->getUserParameters($request));
        } else {
            return view('content.home.index');
        }
    }

    /**
     * @return Guard
     */
    private function guard()
    {
        return Auth::guard();
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getUserParameters(Request $request)
    {
        return [
            'name' => $request->user()->name,
            'image' => Storage::url($request->user()->image ?? 'images/users/default.png'),
        ];
    }
}
