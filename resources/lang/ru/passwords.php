<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'password' => 'Passwords must be at least six characters and match the confirmation.',
    'reset' => 'Your password has been reset!',
    'sent' => 'We have e-mailed your password reset link!',
    'token' => 'This password reset token is invalid.',
    'user' => "We can't find a user with that e-mail address.",
    'mail' => [
        'subject' => 'Password repair in ' . config('app.name'),
        'header' => 'Reparation password request',
        'lines' => [
            'You are receiving this email because we received a password reset request for your account.',
            'Press the button to proceed password repair.',
            'If you did not request a password reset, no further action is required.'
        ],
        'actionText' => 'Repair password',
    ]

];
