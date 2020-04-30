<?php

namespace Modules\Sitemap\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\Models\Widget;
use Location;
use Setting;
use MetaTag;
use LaravelLocalization;
use Theme;
use Kris\LaravelFormBuilder\FormBuilder;

class SitemapController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(FormBuilder $formBuilder)
    {
        $settings = Setting::all();
        return view('sitemap::admin.index', compact('settings'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $params = $request->all();

        Setting::set('sitemap_cache', $request->input('sitemap_cache'));
        Setting::set('sitemap_entries', $request->input('sitemap_entries'));
        Setting::save();

        alert()->success('Successfully saved');
        return redirect()->route('panel.addons.sitemap.index');
    }

}