<?php

namespace Modules\Ratings\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\Models\Comment;
use Location;
use Setting;
use MetaTag;
use LaravelLocalization;
use Theme;
use Kris\LaravelFormBuilder\FormBuilder;

class ReviewsController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:admin')->except(['comments', 'edit', 'update']);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(FormBuilder $formBuilder)
    {
        $settings = Setting::all();
        $form = $formBuilder->create('Modules\Ratings\Forms\RatingsForm', [
            'method' => 'POST',
            'url' => route('panel.addons.ratings.store'),
        ], $settings);
        return view('ratings::admin.index', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $report = Comment::findOrFail($id);
        $report->approved = (bool) $request->input('approved');
        $report->comment = $request->input('comment');
        $report->rate = $request->input('rate');
        $report->save();

        alert()->success('Successfully saved');
        return redirect()->route('panel.addons.ratings.comments');
    }

    public function edit($id, FormBuilder $formBuilder)
    {
        $comment = Comment::find($id);
        $form = $formBuilder->create('Modules\Ratings\Forms\CommentForm', [
            'method' => 'PUT',
            'url' => route('panel.addons.ratings.update', [$comment->id]),
            'model' => $comment
        ]);
        return view('ratings::admin.edit', compact('form'));
    }

    public function comments(FormBuilder $formBuilder)
    {
        $comments = Comment::orderBy('created_at', 'DESC')->paginate(15);
        return view('ratings::admin.comments', compact('comments'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $params = $request->all();

        Setting::set('rating_profile_page', (boolean) $request->has('rating_profile_page'));
        Setting::set('rating_listing_page', (boolean) $request->has('rating_listing_page'));
        Setting::set('rating_permission_group', $request->input('rating_permission_group'));
        Setting::set('rating_profile_limit', $request->input('rating_profile_limit'));
        Setting::set('rating_listing_limit', $request->input('rating_listing_limit'));

        Setting::save();

        alert()->success('Successfully saved');
        return redirect()->route('panel.addons.ratings.index');
    }

}