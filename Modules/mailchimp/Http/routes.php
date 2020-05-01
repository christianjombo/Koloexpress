<?php

Route::group(['middleware' => 'web', 'prefix' => 'panel/addons', 'as' => 'panel.addons.', 'middleware' => ['web', 'role:admin'], 'namespace' => 'Modules\MailChimp\Http\Controllers'], function()
{
    Route::resource('mailchimp', 'MailChimpController');
});