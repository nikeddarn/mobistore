<?php

namespace App\Http\Controllers\Profile;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Proengsoft\JsValidation\Facades\JsValidatorFacade;

class UserProfileController extends Controller
{
    /**
     * Show user profile view with user profile data.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showUserProfile(Request $request)
    {
        return view('content.user.profile.show.index')->with($this->userProfileData($request->user()));
    }

    /**
     * Show user profile view with user profile data.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showUserProfileForm(Request $request)
    {
        $user = $request->user();

        return view('content.user.profile.change.index')->with($this->userProfileData($user))->with($this->jsValidators($user));
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
            'email' => $user->email,
            'phone' => $user->phone,
            'city' => $user->city,
            'site' => $user->site,
            'image' => Storage::url($user->image),
        ];
    }

    /**
     * Check and store received user profile data.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveUserProfile(Request $request)
    {

        $this->validate($request, $this->rules($request->user()));

        $this->storeProfileData($request);

        return redirect('/');
    }

    /**
     * @param Request $request
     * @internal param array $data
     * @internal param User $user
     */
    private function storeProfileData(Request $request)
    {
        $user = $request->user();

        if ($request->has('name')) {
            $nameParts = explode(' ', $request->get('name'));
            array_walk($nameParts, function (&$namePart) {
                $namePart = ucfirst($namePart);
            });
            $user->name = implode(' ', $nameParts);
        }

        if ($request->has('email')) {
            $user->email = $request->get('email');
        }

        if ($request->has('phone')) {
            $user->phone = $request->get('phone');
        }

        if ($request->has('city')) {
            $user->city = $request->get('city');
        }

        if ($request->has('site')) {
            $site = $request->get('site');
            if (strpos($site, '://')) {
                $user->site = $site;
            } else {
                $user->site = 'http://' . $site;
            }
        }

        if ($request->hasFile('image')) {
            $user->image = $request->image->store('images/users', 'public');
        }

        $user->save();
    }

    /**
     * Create JsValidators for login and registration forms.
     *
     * @param User $user
     * @return array
     */
    private function jsValidators(User $user)
    {
        return [
            'profileFormValidator' => JsValidatorFacade::make($this->rules($user))
        ];
    }

    /**
     * User profile validation rules.
     *
     * @param User $user
     * @return array
     */
    private function rules(User $user)
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'regex:/[\s\+\(\)\-0-9]{10,24}/',
            'city' => 'max:64',
            'site' => 'max:255',
            'image' => 'image|max:' . intval(ini_get('upload_max_filesize')) * 1024,
        ];
    }
}
