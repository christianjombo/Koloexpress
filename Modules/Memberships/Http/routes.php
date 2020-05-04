<?php

Route::group(['middleware' => 'web', 'prefix' => 'panel/addons', 'as' => 'panel.addons.', 'middleware' => ['web', 'role:admin'], 'namespace' => 'Modules\Memberships\Http\Controllers\Admin'], function()
{
    Route::get('/memberships/subscriptions', 'MembershipsController@subscriptions')->name('memberships.subscriptions');

    Route::resource('memberships', 'MembershipsController');
});

Route::group(['prefix' => LaravelLocalization::setLocale(), 'namespace' => 'Modules\Memberships\Http\Controllers', 'middleware' => ['web','jailBanned', 'auth']], function()
{
    Route::get('/listing/{listing}/membership', 'MembershipsController@getPayment')->name('addons.memberships.payment');
    Route::post('/listing/{listing}/membership', 'MembershipsController@postPayment')->name('addons.memberships.update');


    Route::get('/membership/subscribe', 'SubscribeController@index')->name('addons.memberships.subscribe');

    #stripe controller
    Route::get('/membership/stripe/subscribe', 'StripeController@index')->name('addons.memberships.stripe.subscribe');
    #Route::post('/membership/stripe/subscribe', 'StripeController@confirmation')->name('addons.memberships.stripe.subscription_confirmation');
    Route::post('/membership/stripe/switch', 'StripeController@switch_plan')->name('addons.memberships.stripe.switch');
    Route::any('/membership/stripe/confirmation', 'StripeController@confirmation')->name('addons.memberships.stripe.confirmation');

    #paypal controller
    Route::get('/membership/paypal/redirect', 'PaypalController@redirect')->name('addons.memberships.paypal.redirect');
    Route::get('/membership/paypal/subscribe', 'PaypalController@index')->name('addons.memberships.paypal.subscribe');
    Route::post('/membership/paypal/subscribe', 'PaypalController@confirmation')->name('addons.memberships.paypal.subscription_confirmation');
    Route::post('/membership/paypal/switch', 'PaypalController@switch_plan')->name('addons.memberships.paypal.switch');
    Route::any('/membership/paypal/confirmation', 'PaypalController@confirmation')->name('addons.memberships.paypal.subscription_confirmation');
    Route::any('/membership/paypal/return_url', 'PaypalController@return_url')->name('addons.memberships.paypal.return');
    Route::any('/membership/paypal/cancel_url', 'PaypalController@cancel_url')->name('addons.memberships.paypal.cancel');

});


Route::group(['prefix' => LaravelLocalization::setLocale(), 'namespace' => 'Modules\Memberships\Http\Controllers', 'middleware' => ['web','jailBanned']], function()
{
    Route::any('/membership/stripe/webhook', 'StripeController@webhook')->name('addons.memberships.stripe.webhook');
    Route::any('/membership/paypal/webhook', 'PaypalController@webhook')->name('addons.memberships.paypal.webhook');
});