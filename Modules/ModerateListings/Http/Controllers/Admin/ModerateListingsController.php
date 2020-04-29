<?php

namespace Modules\ModerateListings\Http\Controllers\Admin;

use App\Models\ReportedListing;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Kris\LaravelFormBuilder\FormBuilder;
use Carbon\Carbon;

class ModerateListingsController extends Controller
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

        return view('moderatelistings::admin.index', $data);

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
        dd(5);
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
    public function edit($id, FormBuilder $formBuilder)
    {
        $data = [];

        $report = ReportedListing::find($id);
        $data['report'] = $report;
        $form = $formBuilder->create('Modules\ModerateListings\Forms\ReportForm', [
            'method' => 'PUT',
            'url' => route('panel.addons.moderatelistings.update', $report),
            'model' => $report
        ]);
        $data['form'] = $form;
        return view('moderatelistings::admin.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {

        if($request->input('action') == 'disable_listing') {
            $listing = Listing::find($id);
            $listing->is_disabled = Carbon::now();
            $listing->save();
        }

        $report = ReportedListing::findOrFail($id);
        $report->active = (bool) $request->input('active');
        $report->moderator_id = auth()->user()->id;
        $report->moderator_message = $request->input('moderator_message');
        $report->save();

        alert()->success('Successfully saved');
        return redirect()->route('panel.addons.moderatelistings.index');
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
