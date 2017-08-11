<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/******************* Common Routes *************************************************/

// Change language
Route::get('/language/{language}', 'Localization\LanguageController@changeLanguage');


/******************* Static Pages *************************************************/
Route::get('/', 'MainController@showMainPage');

Route::get('/warranty', function () {
    return view('content.warranty.index');
});
Route::get('/about', function () {
    return view('content.about.index');
});
Route::get('/wholesale', function () {
    return view('content.wholesale.index');
});
Route::get('/retail', function () {
    return view('content.retail.index');
});
Route::get('/partner', function () {
    return view('content.partner.index');
});
Route::get('/manufacturer', function () {
    return view('content.manufacturer.index');
});


/***************************** Auth Routes ************************/

Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');

Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

Route::post('/register', 'Auth\RegisterController@register');

Route::get('/forgot', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
Route::post('/forgot', 'Auth\ForgotPasswordController@sendResetLinkEmail');

Route::get('/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('/reset', 'Auth\ResetPasswordController@reset');


/******************* User Pages *************************************************/

Route::middleware(['auth:web'])->group(function () {

    Route::get('/user', 'Profile\UserProfileController@showUserProfile');

    Route::get('/user/profile', 'Profile\UserProfileController@showUserProfileForm');
    Route::post('/user/profile', 'Profile\UserProfileController@saveUserProfile');

    Route::get('/user/password', 'Profile\ChangePasswordController@showChangePasswordForm');
    Route::post('/user/password', 'Profile\ChangePasswordController@changePassword');

    Route::get('/user/settings', 'Profile\SettingsController@showSettingsForm');
    Route::post('/user/settings', 'Profile\SettingsController@resetSettings');

    Route::get('/user/communication', 'Communication\MessageController@showMessages');
    Route::get('/user/account', 'Account\AccountController@showUserAccount');
    Route::get('/user/order', 'Order\OrderController@showOrders');
    Route::get('/user/delivery', 'Delivery\DeliveryController@showUserDeliveries');
    Route::get('/user/warranty', 'Warranty\WarrantyController@showUserWarranties');

});
