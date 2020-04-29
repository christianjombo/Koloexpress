<?php

namespace Modules\Homepage\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Widget;
use Location;
use Setting;
use MetaTag;
use LaravelLocalization;
use Theme;



class HomepageController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $current_locale = LaravelLocalization::getCurrentLocale();
        $data['widgets'] = Widget::where('locale', $current_locale)->orderBy('position', 'ASC')->get();
        $data['show_search'] = false;

        MetaTag::set('title', Setting::get('home_title'));
        MetaTag::set('description', Setting::get('home_description'));

        return view('homepage::index', $data);

        return view('homepage::index');
    }
}
