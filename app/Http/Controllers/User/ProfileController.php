<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show user profile.
     *
     * @return View
     */
    public function show():View
    {
        return view('content.user.profile.show.index')->with([
            'userProfile' => auth('web')->user(),
            'commonMetaData' => [
                'title' => trans('meta.title.user.profile.show'),
            ],
        ]);
    }

    /**
     * Show user profile form with user profile data.
     *
     * @return View
     */
    public function showProfileForm()
    {
        $user = auth('web')->user();

        return view('content.user.profile.edit.index')->with([
            'userProfile' => $user,
            'commonMetaData' => [
                'title' => trans('meta.title.user.profile.edit'),
            ],
        ]);
    }

    /**
     * Check and store received user profile data.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {

        $this->validate($request, $this->rules($request->user()));

        $this->storeProfileData($request);

        return redirect(route('profile.show'));
    }

    /**
     * @param Request $request
     * @internal param array $data
     * @internal param User $user
     */
    private function storeProfileData(Request $request)
    {
        $userData = [];

        $nameParts = explode(' ', $request->get('name'));
        array_walk($nameParts, function (&$namePart) {
            $namePart = ucfirst($namePart);
        });
        $userData['name'] = implode(' ', $nameParts);

        if ($request->has('site')) {
            $site = $request->get('site');
            if (!strpos($site, '://')) {
                $site = 'http://' . $site;
            }
            $userData['site'] = $site;
        }

        if ($request->hasFile('image')) {
            $userData['image'] = $request->image->store('images/users', 'public');
        }

        if ($request->has('city')) {
            $userData['city'] = Str::ucfirst($request->get('city'));
        }

        $request->user()->update(array_merge($userData), $request->only(['email', 'phone']));
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
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id, 'id')],
            'phone' => ['nullable', 'regex:/^[\s\+\(\)\-0-9]{10,24}$/'],
            'city' => 'max:64',
            'site' => 'max:255',
            'image' => 'image|max:' . intval(ini_get('upload_max_filesize')) * 1024,
        ];
    }
}
