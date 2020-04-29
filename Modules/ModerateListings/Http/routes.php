<?php

Route::group(['middleware' => 'web', 'prefix' => 'panel/addons', 'as' => 'panel.addons.', 'middleware' => ['web', 'role:admin|moderator'], 'namespace' => 'Modules\ModerateListings\Http\Controllers\Admin'], function()
{


    Route::group(['prefix' => 'moderatelistings', 'as' => 'moderatelistings.', 'middleware' => ['web', 'role:admin']], function()
    {
        Route::resource('report-types', 'ReportTypesController');
    });

    Route::resource('moderatelistings', 'ModerateListingsController');


});

Route::group(['prefix' => LaravelLocalization::setLocale(), 'namespace' => 'Modules\ModerateListings\Http\Controllers', 'middleware' => ['web','jailBanned']], function()
{
    Route::get('/report/{listing}', 'ModerateListingsController@report')->name('listing.report');
    Route::post('/report/{listing}', 'ModerateListingsController@submit')->name('listing.report');
});
