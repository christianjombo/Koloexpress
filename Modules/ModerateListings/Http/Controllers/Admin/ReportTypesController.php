<?php

namespace Modules\ModerateListings\Http\Controllers\Admin;

use App\Models\ReportedListing;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Setting;
class ReportTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $data = [];
        $listings = ReportedListing::orderBy('created_at', 'desc');
        $data['listings'] = $listings->paginate(10);
        $report_types = Setting::get('moderatelistings.report_types');

        $data['report_types'] = $report_types;

        return view('moderatelistings::admin.report_types.index', $data);

    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $data = [];

        $listings = ReportedListing::orderBy('created_at', 'desc');
        $data['listings'] = $listings->paginate(10);



        return view('moderatelistings::admin.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        Setting::set('moderatelistings.report_types', $request->input('report_name'));
        Setting::save();

        alert()->success('Successfully saved');
        return redirect()->route('panel.addons.moderatelistings.report-types.index');

        #dd($request->input('report_name'));
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('moderatelistings::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('moderatelistings::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
