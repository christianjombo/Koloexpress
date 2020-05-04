<?php

namespace Modules\Ratings\Http\Controllers;

use App\Http\Requests\StoreComment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\Models\Filter;
use App\Models\Listing;
use App\Models\Category;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Location;
use Mapper;
use MetaTag;

class ReviewsController extends Controller
{

    protected $category_id;

    public function __construct() {
           $this->middleware('auth', ['except' => ['index']]);
     }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($listing, $slug, Request $request)
    {
        #dd(\Config::get('view.paths'));
        $data = [];
        $data['has_map'] = false;
        $breadcrumbs = [];
        $category = $listing->category;
        while($category = $category->child) {
            $breadcrumbs[] = $category;
        }
        $data['breadcrumbs'] = array_reverse($breadcrumbs);
        if($listing->location) {
            Mapper::map($listing->location->getLat(), $listing->location->getLng(), ['zoom' => 14, 'zoomControl' => false, 'streetViewControl' => false, 'mapTypeControl' => false, 'center' => true, 'marker' => true]);
            $data['has_map'] = true;
        }

        $data['listing'] = $listing;
        $data['comments'] = $listing->comments()->with('commenter')->paginate(2);

        if(view()->exists('reviews.index')){
            return view('reviews.index', $data);
        }
        return view('ratings::reviews.index', $data);
    }


    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create($listing, $slug)
    {

        //has user ordered item?
        $user = auth()->user();
        $has_ordered = Order::where('user_id', $user->id)->where('status', 'accepted')->count();

        $data = [];
        $data['has_map'] = false;
        $data['listing'] = $listing;
        $data['has_ordered'] = $has_ordered;
        if(view()->exists('reviews.create')){
            return view('reviews.create', $data);
        }
        return view('ratings::reviews.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(StoreComment $request, $listing, $slug)
    {
        auth()->user()->comment($listing, $request->get('comment'), $request->get('score'));
		alert()->success(__('Thanks for submitting a review!'));
        return redirect(dirname(url()->current()));
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function profile($user, Request $request)
    {
        $data = [];
        $data['profile'] = $user;
        $data['comments'] = $user->comments()->with('commenter')->paginate(20);

        MetaTag::set('title', __(":name's reviews", ['name' => $user->display_name]));
        MetaTag::set('description', $user->bio);
        MetaTag::set('image', url($user->avatar));

        return view('profile.reviews', $data);
    }
}
