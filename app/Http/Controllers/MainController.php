<?php

namespace App\Http\Controllers;


class MainController extends Controller
{
    /**
     * Return main view.
     *
     * @return \Illuminate\View\View
     */
    public function showMainPage()
    {
        return view('content.home.index');
    }
}
