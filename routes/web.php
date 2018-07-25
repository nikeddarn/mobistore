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

    Route::get('/user/balance', 'User\BalanceController@show')->name('user_balance.show');

    Route::get('/user/notifications', 'User\ActualNotificationController@show')->name('user_notifications.show.unread');
    Route::get('/user/notifications/mark/{id}', 'User\ActualNotificationController@markAsRead');
    Route::get('/user/notifications/all', 'User\NotificationController@show')->name('user_notifications.show.all');

    Route::get('/user/shipments', 'User\ShipmentController@show')->name('user_shipments.show');

    Route::get('/user/orders', 'User\OrderController@show')->name('user_orders.show');

    Route::get('/user/reclamations', 'User\ReclamationController@show')->name('user_reclamations.show');

    Route::get('/user/payments', 'User\WarrantyController@show')->name('user_payments.show');

    Route::get('/user/profile', 'User\ProfileController@show')->name('user_profile.show');
//
    Route::get('/user/profile/edit', 'User\ProfileController@showProfileForm')->name('profile.edit');

    Route::post('/user/profile/set', 'User\ProfileController@save')->name('profile.set');
//
    Route::get('/user/password', 'User\PasswordController@showChangePasswordForm')->name('password.show');

    Route::post('/user/password/reset', 'User\PasswordController@changePassword')->name('user_password.reset');

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

/******************* Vendor Pages *************************************************/

Route::middleware('admin.vendor')->group(function () {

    Route::get('/partner', 'Vendor\VendorController@index');

    Route::get('/partner/{vendorId}/account', 'Vendor\VendorAccountController@index')->name('vendor.account');

    Route::get('/partner/{vendorId}/order', 'Vendor\VendorOrderController@index')->name('vendor.order');
    Route::post('/partner/order/collect', 'Vendor\VendorCollectOrderController@collect')->name('vendor.order.collect');
    Route::post('/partner/order/collect/all', 'Vendor\VendorCollectOrderController@collectAll')->name('vendor.order.collect.all');

    Route::get('/partner/{vendorId}/delivery', 'Vendor\VendorDeliveryController@index')->name('vendor.delivery');
    Route::post('/partner/delivery/invoices/add_to_shipment', 'Vendor\VendorDeliveryController@addInvoicesToShipment')->name('vendor.delivery.invoices.add_to_shipment');

    Route::get('/partner/{vendorId}/warranty', 'Vendor\VendorWarrantyController@index')->name('vendor.warranty');

    Route::get('/partner/{vendorId}/payment', 'Vendor\VendorPaymentController@index')->name('vendor.payment');

    Route::get('/partner/{vendorId}/shipment', 'Vendor\VendorShipmentController@index')->name('vendor.shipment');
    Route::post('/partner/shipment/create/schedule', 'Vendor\VendorShipmentController@createFromSchedule')->name('vendor.shipment.create.schedule');
    Route::post('/partner/shipment/create/date', 'Vendor\VendorShipmentController@createByDate')->name('vendor.shipment.create.date');
    Route::post('/partner/shipment/dispatch', 'Vendor\VendorShipmentController@dispatchShipment')->name('vendor.shipment.dispatch');
    Route::post('/partner/shipment/remove', 'Vendor\VendorShipmentController@removeShipment')->name('vendor.shipment.remove');

    Route::get('/partner/{vendorId}/courier', 'Vendor\VendorCourierController@index')->name('vendor.courier');
    Route::post('/partner/courier/create', 'Vendor\VendorCourierController@createCourier')->name('vendor.courier.create');
    Route::post('/partner/courier/create_tour', 'Vendor\VendorCourierController@createCourierTour')->name('vendor.courier.create_tour');


});

/******************* Storage Pages *************************************************/

Route::middleware('admin.storage')->group(function () {

    // list of storages
    Route::get('/store', 'Storage\StorageController@index');

    // products on storage
    Route::get('/store/{storageId}/product', 'Storage\StorageProductController@index')->name('storage.product');

    // incoming invoices handling
    Route::get('/store/{storageId}/incoming', 'Storage\StorageIncomingController@index')->name('storage.incoming');
    Route::post('/store/incoming/receive/invoice', 'Storage\StorageIncomingController@receiveProductInvoice')->name('storage.incoming.receive.invoice');
    Route::post('/store/incoming/receive/shipment', 'Storage\StorageIncomingController@receiveShipment')->name('storage.incoming.receive.shipment');

    // outgoing invoices handling
    Route::get('/store/{storageId}/outgoing', 'Storage\StorageOutgoingСontroller@index')->name('storage.outgoing');

    Route::get('/store/{storageId}/delivery', 'Storage\StorageDeliveryСontroller@index')->name('storage.delivery');

    Route::get('/store/{storageId}/shipment', 'Storage\StorageShipmentСontroller@index')->name('storage.shipment');

    Route::get('/store/{storageId}/warranty', 'Storage\StorageWarrantyСontroller@index')->name('storage.warranty');

    Route::get('/store/{storageId}/payment', 'Storage\StoragePaymentСontroller@index')->name('storage.payment');
//
//    Route::post('/partner/order/collect', 'Vendor\VendorCollectOrderController@collect')->name('vendor.order.collect');
//    Route::post('/partner/order/collect/all', 'Vendor\VendorCollectOrderController@collectAll')->name('vendor.order.collect.all');
//
//    Route::get('/partner/{vendorId}/delivery', 'Vendor\VendorDeliveryController@index')->name('vendor.delivery');
//    Route::post('/partner/delivery/invoices/add_to_shipment', 'Vendor\VendorDeliveryController@addInvoicesToShipment')->name('vendor.delivery.invoices.add_to_shipment');
//
//    Route::get('/partner/{vendorId}/warranty', 'Vendor\VendorWarrantyController@index')->name('vendor.warranty');
//
//    Route::get('/partner/{vendorId}/payment', 'Vendor\VendorPaymentController@index')->name('vendor.payment');
//
//    Route::get('/partner/{vendorId}/shipment', 'Vendor\VendorShipmentController@index')->name('vendor.shipment');
//    Route::post('/partner/shipment/create/schedule', 'Vendor\VendorShipmentController@createFromSchedule')->name('vendor.shipment.create.schedule');
//    Route::post('/partner/shipment/create/date', 'Vendor\VendorShipmentController@createByDate')->name('vendor.shipment.create.date');
//    Route::post('/partner/shipment/dispatch', 'Vendor\VendorShipmentController@dispatchShipment')->name('vendor.shipment.dispatch');
//    Route::post('/partner/shipment/remove', 'Vendor\VendorShipmentController@removeShipment')->name('vendor.shipment.remove');
//
//    Route::get('/partner/{vendorId}/courier', 'Vendor\VendorCourierController@index')->name('vendor.courier');
//    Route::post('/partner/courier/create', 'Vendor\VendorCourierController@createCourier')->name('vendor.courier.create');
//    Route::post('/partner/courier/create_tour', 'Vendor\VendorCourierController@createCourierTour')->name('vendor.courier.create_tour');


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

Route::get('/favourite', 'Product\ProductFavouriteController@show')->name('product.favourite');

Route::get('/favourite/add/{id}', 'Product\ProductFavouriteController@addToFavourite')->middleware(['auth:web']);

Route::get('/favourite/remove/{id}', 'Product\ProductFavouriteController@removeFromFavourite')->middleware(['auth:web']);

//******************************************** Recent Products **********************************

Route::get('/recent', 'Product\ProductRecentController@show')->name('product.recent');

//******************************************** Action Products **********************************

Route::get('/actions', 'Product\ProductActionController@show')->name('product.action');

//************************************************ User Cart ****************************************

Route::get('/cart', 'Cart\CartController@show')->name('cart.show');

Route::get('/cart/add/{id}', 'Cart\CartController@add');

Route::get('/cart/remove/{id}', 'Cart\CartController@remove')->name('cart.remove');

Route::post('/cart/set/count', 'Cart\CartController@setCount')->name('cart.set.count');

//************************************************ Checkout ****************************************

Route::get('/checkout', 'Checkout\ShowCheckoutController@show')->name('checkout.show');

Route::post('/checkout/confirm', 'Checkout\ConfirmCheckoutController@confirmOrder')->name('checkout.confirm');

//************************************************ Payment ****************************************

Route::post('/payment', 'Payment\CheckoutController@')->name('payment.show');
