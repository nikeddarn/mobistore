<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Proengsoft\JsValidation\Facades\JsValidatorFacade;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/user/communication';

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('content.auth.login.index')->with($this->jsValidators());
    }

    /**
     * Create JsValidators for login and registration forms.
     *
     * @return array
     */
    private function jsValidators()
    {
        return [
            'loginFormValidator' => JsValidatorFacade::make([
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:6',
            ]),
            'registrationFormValidator' => JsValidatorFacade::make([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]),
        ];
    }
}
