<?php

Route::group(['middleware' => 'web', 'prefix' => 'panel/addons', 'as' => 'panel.addons.', 'middleware' => ['web', 'role:admin|moderator'], 'namespace' => 'Modules\Ratings\Http\Controllers\Admin'], function()
{
    Route::get('/ratings/comments', 'ReviewsController@comments')->name('ratings.comments');
    Route::resource('ratings', 'ReviewsController');
});

Route::group(['prefix' => LaravelLocalization::setLocale(), 'namespace' => 'Modules\Ratings\Http\Controllers', 'middleware' => ['web','jailBanned']], function()
{
    Route::get('/profile/{user}/reviews', 'ReviewsController@profile')->name('profile.reviews');

    Route::group(['prefix' => 'listing'], function()
    {
        Route::resource('/{listing}/{slug}/reviews', 'ReviewsController');
    });

});