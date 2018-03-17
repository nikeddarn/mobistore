<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasswordController extends Controller
{
    /**
     * Show change password form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showChangePasswordForm(Request $request)
    {
        return view('content.user.password.index')->with([
            'commonMetaData' => [
                'title' => trans('meta.title.user.password'),
            ],
        ]);
    }

    /**
     * Check and reset user password.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $this->validator($request->except('_token'), $user)->validate();

        $this->resetPassword($request->only('password'), $user);

        return redirect(route('profile.show'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @param User $user
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, User $user)
    {
        Validator::extend('old_password_confirmed', function ($attribute, $value) use ($user){
            return Hash::check($value, $user->password);
        });

        return Validator::make($data, [
            'old_password' => 'required|min:6|old_password_confirmed',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Reset user password.
     *
     * @param array $data
     * @param User $user
     */
    private function resetPassword(array $data, User $user)
    {
        $user->password = Hash::make($data['password']);

        $user->save();
    }
}
