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


use Illuminate\Support\Facades\Route;

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
Route::get('/admin/login', 'Auth\LoginController@showAdminLoginForm')->name('admin.login');
Route::post('/login', 'Auth\LoginController@login');

Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

Route::post('/register', 'Auth\RegisterController@register');

Route::get('/forgot', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
Route::post('/forgot', 'Auth\ForgotPasswordController@sendResetLinkEmail');

Route::get('/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('/reset', 'Auth\ResetPasswordController@reset');


/******************* User Pages *************************************************/

// auth middleware with parameter : 'web' (guard name)
Route::middleware(['auth:web'])->group(function () {

    Route::get('/profile', 'Profile\UserProfileController@showUserProfile')->name('profile.show');

    Route::get('/user/profile/change', 'Profile\UserProfileController@showUserProfileForm');
    Route::post('/user/profile/set', 'Profile\UserProfileController@saveUserProfile');

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


/******************* Admin Pages *************************************************/

/**
 * ****************************************************************
* ToDo this routes must be removed after setup completed !!!!!!!!!!!
 * ****************************************************************
 */
Route::get('/initialize', 'Admin\SetupController@initialize');
Route::get('/setup', 'Admin\SetupController@setup');
// *****************************************************************

Route::middleware(['admin'])->group(function () {

//    Route::get('/setup/categories', 'Admin\SetupController@categories');

//    Route::get('/setup/products', 'Admin\SetupController@products');

    Route::get('/setup/watermark', 'Admin\SetupController@watermark');

    Route::get('/badges', 'Admin\Support\InitializeApplication@insertBadges');

//    Route::post('/setup/confirm', 'Admin\SetupController@confirmSetup');




    Route::get('/admin', 'Admin\AdminController@index');

    Route::get('/admin/categories', 'Admin\CategoriesController@index');

});

/**************************** Shop Pages *********************************************/

// products list by categories
Route::get('/category/{url?}', 'Shop\Single\CategoryUnfilteredController@index')->where(['url' => '.*'])->name('product.category');
Route::get('/filter/category/{url?}', 'Shop\Multiply\CategoryFilteredController@index')->where(['url' => '.*']);

// products list by brands
Route::get('/brand/{url?}', 'Shop\Single\BrandUnfilteredController@index')->where(['url' => '.*'])->name('product.brand');
Route::get('/filter/brand/{url?}', 'Shop\Multiply\BrandFilteredController@index')->where(['url' => '.*']);

// product detail page
Route::get('/product/{url}', 'Product\ProductDetailsController@index');

// ************************ Product Details Page. Comments ************************************

Route::post('/comment/product', 'Comment\ProductCommentsController@store')->name('comment.insert');

Route::get('/comment/product/{id}', 'Comment\ProductCommentsController@index');

//******************************************** Favourite Products **********************************

Route::get('/favourite', 'Product\ProductFavouriteController@show')->middleware(['auth:web'])->name('product.favourite');

Route::get('/favourite/add/{id}', 'Product\ProductFavouriteController@addToFavourite')->middleware(['auth:web']);

Route::get('/favourite/remove/{id}', 'Product\ProductFavouriteController@removeFromFavourite')->middleware(['auth:web']);

//******************************************** Recent Products **********************************

Route::get('/recent', 'Product\ProductRecentController@show')->middleware(['auth:web'])->name('product.recent');

//******************************************** Action Products **********************************

Route::get('/actions', 'Product\ProductActionController@show')->middleware(['auth:web'])->name('product.action');

//************************************************ User Cart ****************************************

Route::get('/cart', 'Cart\CartController@show')->name('cart.show');

Route::get('/cart/add/{id}', 'Cart\CartController@add');

Route::get('/cart/remove/{id}', 'Cart\CartController@remove')->name('cart.remove');

Route::post('/cart/set/count', 'Cart\CartController@setCount')->name('cart.set.count');

//************************************************ Checkout ****************************************

Route::get('/checkout', 'Checkout\CheckoutController@show')->name('checkout.show');

//************************************************ Payment ****************************************

Route::post('/payment', 'Payment\CheckoutController@')->name('payment.show');
