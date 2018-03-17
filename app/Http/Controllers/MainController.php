<?php

namespace App\Http\Controllers;


class MainController extends Controller
{
    /**
     * Return main view or home user view if authenticated.
     *
     * @return \Illuminate\View\View
     */
    public function showMainPage()
    {
        return view('content.home.index');
    }
}
