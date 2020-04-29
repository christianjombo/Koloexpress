<?php


Route::group(['middleware' => 'web', 'prefix' => 'panel/addons', 'as' => 'panel.addons.', 'middleware' => ['web', 'role:admin'], 'namespace' => 'Modules\Homepage\Http\Controllers\Admin'], function()
{

    Route::resource('homepage', 'HomepageController');

});

Route::group(['middleware' => 'web', 'prefix' => LaravelLocalization::setLocale(), 'namespace' => 'Modules\Homepage\Http\Controllers'], function()
{
    Route::get('/', 'HomepageController@index')->name('home');
});
