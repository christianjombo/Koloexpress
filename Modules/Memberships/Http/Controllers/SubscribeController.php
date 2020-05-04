<?php

namespace Modules\Memberships\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Kris\LaravelFormBuilder\FormBuilder;
use Gerardojbaez\Laraplans\Models\Plan;
use Gerardojbaez\Laraplans\Models\PlanFeature;
use Auth;
use Carbon\Carbon;

class SubscribeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        //show list of current plans
        #show pricing plans
        $plans = Plan::orderBy('price', 'ASC')->get();
        $user = auth()->user();
        $free_plan = $this->getFreePlan($plans);

        #if no active plan, move user to free plan
        if(!$user->subscription('main') || !$user->subscription('main')->isActive()) {
            $user->newSubscription('main', $free_plan)->create();
            unset($user->new_membership_plan);
            $user->save();
            return redirect(url()->full());
        }

        $days_remaining = $user->subscription('main')->ends_at->diffInDays();
        $next_plan = null;
        $next_plan = ($user->subscription('main')->isCanceled())?$free_plan:null;
        if($user->new_membership_plan) {
            $next_plan = Plan::find($user->new_membership_plan);
        }

        $selected_plan = $user->subscription('main')->plan;
        $options = [
            'listings' => __("Listings"),
            'featured_listings' => __("Featured listings"),
            'priority_listings' => __("Priority listings"),
            'images' => __("Images per listing"),
            'bold_listings' => __("Bold listings"),
            'messages' => __("Messages"),
        ];

        $subscribed_user = ($selected_plan->price > 0)?true:false;
        $current_gateway = null;
        if($user->membership_paypal_subscription) {
            $current_gateway = 'paypal';
        }
        if($user->membership_stripe_subscription) {
            $current_gateway = 'stripe';
        }
        if(!$subscribed_user || $next_plan && $next_plan->price == 0) {
            $current_gateway = null;
        }

        $listing_id = $request->input('listing_id', 0);
        $listing = null;
        if($listing_id) {
            $listing = Listing::find($listing_id);
        }

        return view('memberships::subscribe', compact('plans', 'selected_plan', 'user', 'options', 'subscribed_user', 'current_gateway', 'listing', 'next_plan', 'days_remaining'));
    }

    private function getFreePlan($plans) {
        foreach($plans as $plan) {
            if($plan->isFree())
                return $plan;
        }
        return null;
    }

}
