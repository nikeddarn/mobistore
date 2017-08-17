<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function showAdminPage(Request $request)
    {
        return view('content.admin.home.index')->with($this->getUserParameters($request));
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
