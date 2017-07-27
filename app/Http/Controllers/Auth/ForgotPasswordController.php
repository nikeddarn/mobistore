<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Proengsoft\JsValidation\Facades\JsValidatorFacade;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('content.auth.forgot.index')->with($this->jsValidators());
    }

    /**
     * Create JsValidators for login and registration forms.
     *
     * @return array
     */
    private function jsValidators()
    {
        return [
            'forgotFormValidator' => JsValidatorFacade::make([
                'email' => 'required|string|email|max:255',
            ]),
        ];
    }
}
