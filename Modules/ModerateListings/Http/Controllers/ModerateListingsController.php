<?php

namespace Modules\ModerateListings\Http\Controllers;

use App\Models\ReportedListing;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ModerateListingsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function report($listing)
    {
        $data = [];
        $data['listing'] = $listing;

        if(view()->exists('listing.report')){
            return view('listing.report', $data);
        }
        return view('moderatelistings::listing.report', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function submit($listing, Request $request)
    {
        ReportedListing::create([
            'reason' => $request->input('reason'),
            'notes' => $request->input('notes'),
            'user_id' => auth()->user()->id,
            'listing_id' => $listing->id,
        ]);

        alert()->danger(__('Thanks for reporting this listing!'));
        return redirect(route('listing', [$listing, $listing->slug]));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
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
