<?php

namespace App\Http\Controllers\Profile;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Show user settings form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSettingsForm(Request $request)
    {
        return view('content.user.profile.settings.index')->with($this->userProfileData($request->user()))->with($this->userSettings($request->user()));
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


    /**
     * Retrieve current settings from User model.
     *
     * @param User $user
     * @return array
     */
    private function userSettings(User $user)
    {
        return [];
    }

    public function resetSettings(Request $request)
    {
        return redirect('/');
    }
}
