<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    public function showMainPage(){
        if ($this->guard()->check()){
            return view('')->with($this->getUserParameters());
        }else{
            return view('content.home.guest.index');
        }
    }

    private function guard(){
        return Auth::guard();
    }

    private function getUserParameters(){

    }
}
