<?php

namespace Modules\Memberships\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Kris\LaravelFormBuilder\FormBuilder;
use Gerardojbaez\Laraplans\Models\Plan;
use Gerardojbaez\Laraplans\Models\PlanFeature;
use Auth;
use Carbon\Carbon;

class MembershipsController extends Controller
{
    private function getFreePlan($plans) {
        foreach($plans as $plan) {
            if($plan->isFree())
                return $plan;
        }
        return null;
    }

    public function postPayment(Request $request, $listing) {
        #check quota
        $user = Auth::user();
        $ability = $user->subscription('main')->ability();
        $quota_exceeded = true;

        $duration_units = 1;
        $duration_period = 'month';
        $options = array_merge(['listings'], (array) $request->input('options'));
        foreach($options as $option) {
            if(!$ability->remainings($option)) {
                $quota_exceeded = false;
            }
        }

        if(!$quota_exceeded) {
            alert()->danger(__('Error: You have exceeded your quota.'));
            return back();
        }

        #all good, post the thing
        $listing->is_published = true;
        $listing->is_admin_verified = Carbon::now();

        $listing->expires_at = $this->calculateExpiryTime($listing->expires_at, $duration_units, $duration_period);
        $user->subscriptionUsage('main')->record('listings');
        if(in_array('featured_listings', $options)) {
            $listing->spotlight = $this->calculateExpiryTime($listing->spotlight, $duration_units, $duration_period);
            $user->subscriptionUsage('main')->record('featured_listings');
        }

        if(in_array('priority_listings', $options)) {
            $listing->priority_until = $this->calculateExpiryTime($listing->priority_until, $duration_units, $duration_period);
            $user->subscriptionUsage('main')->record('priority_listings');
        }

        if(in_array('bold_listings', $options)) {
            $listing->bold_until = $this->calculateExpiryTime($listing->bold_until, $duration_units, $duration_period);
            $user->subscriptionUsage('main')->record('bold_listings');
        }

        if(!is_null($user->subscription('main')->ability()->value('images')))
            $listing->photos_limit = $user->subscription('main')->ability()->value('images');

        $listing->save();

        alert()->success(__('Thanks. Your listing has been processed.'));
        return redirect($listing->edit_url);
    }

    private function calculateExpiryTime($old_time, $duration_units, $duration_period) {

        $greatest_time = Carbon::now();
        if($old_time && $old_time->gt($greatest_time)) {
            $greatest_time = $old_time;
        }
        $function = "add".ucwords(str_plural($duration_period));
        return $greatest_time->$function($duration_units);

    }

    public function getPayment($listing)
    {
        #show pricing plans
        $options = [
            ['key' => 'listings', 'label' => __("List on website")],
            ['key' => 'featured_listings', 'label' => __("Make listing featured")],
            ['key' => 'priority_listings', 'label' => __("Increase the priority of listing in searches")],
            ['key' => 'bold_listings', 'label' => __("Make listing bold to stand out")],
        ];
        $plans = Plan::orderBy('price', 'ASC')->get();
        $free_plan = $this->getFreePlan($plans);
        $user = Auth::user();
        $subscription = $user->subscribed('main');

        if(!$subscription) {
            //put on free plan
            if($free_plan) {
                $user->newSubscription('main', $free_plan)->create();
                unset($user->new_membership_plan);
                $user->save();
            }
        }

        $subscription = $user->subscribed('main');

        if(!$subscription) {
            #make user choose a plan
            return redirect()->route('addons.memberships.index');
        }

        #now show user the quota he/she has
        $plan = $user->subscription('main')->plan;
        $ability = $user->subscription('main')->ability();

        return view('memberships::post_listing', compact('listing', 'user', 'ability', 'options', 'plan'));
    }

    public function membership()
    {
        #show pricing plans
        $plans = Plan::orderBy('price', 'ASC')->get();
        $user = auth()->user();
        $default_plan = $plans->first();
        $amount = $default_plan->price;


        return view('memberships::post_listing', compact('plans', 'user', 'amount'));
    }


}
