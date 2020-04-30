<?php

Route::group(['middleware' => 'web', 'prefix' => 'panel/addons', 'as' => 'panel.addons.', 'middleware' => ['web', 'role:admin'], 'namespace' => 'Modules\Sitemap\Http\Controllers\Admin'], function()
{
    Route::resource('sitemap', 'SitemapController');
});

Route::group(['middleware' => 'web', 'namespace' => 'Modules\Sitemap\Http\Controllers'], function()
{
    Route::get('sitemap.xml', 'SitemapController@index');
});