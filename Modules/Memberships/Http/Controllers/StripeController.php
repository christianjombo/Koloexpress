<?php

namespace Modules\Memberships\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Kris\LaravelFormBuilder\FormBuilder;
use Gerardojbaez\Laraplans\Models\Plan;
use Gerardojbaez\Laraplans\Models\PlanFeature;
use Auth;
use Carbon\Carbon;

class StripeController extends SubscribeController
{

    public function confirmation(Request $request) {

        $user = auth()->user();
        \Stripe\Stripe::setApiKey(config('marketplace.stripe_secret_key'));

        $plan = Plan::find($request->input('plan_id'));
        $stripe_plan_id = $this->createOrUpdatePlan($plan);
        $customer = $this->createOrUpdateCustomer($user, $request->input('token.id'));

        $subscription = \Stripe\Subscription::create(array(
            'customer' => $customer->id,
            'items' => [['plan' => $stripe_plan_id]],
            'metadata' => ['plan_id'  =>  $plan->id],
            'prorate' => true,
        ));
        $user->membership_stripe_subscription = $subscription->id;
        unset($user->new_membership_plan);
        $user->save();

        $user->newSubscription('main', $plan)->create();

        #check if this customer has an existing plan. If so cancel plan and re-sub.
        alert()->success(__('Successfully subscribed'));
        return ['status' => true, 'url' => route('addons.memberships.subscribe')];
    }


    public function isDowngrade($new_plan, $old_plan) {
        return ($new_plan->id != $old_plan->id && $new_plan->price < $old_plan->price);
    }

    public function switch_plan(Request $request) {

        \Stripe\Stripe::setApiKey(config('marketplace.stripe_secret_key'));

        $new_plan = $request->input('new_plan');

        $plan = Plan::find($request->input('new_plan'));
        $user = auth()->user();

        $is_downgrade = $this->isDowngrade($plan, $user->subscription('main')->plan);
        $subscription = \Stripe\Subscription::retrieve($user->membership_stripe_subscription);

        if($plan->price > 0) {
            $stripe_plan_id = $this->createOrUpdatePlan($plan);
            \Stripe\Subscription::update($user->membership_stripe_subscription, [
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'plan' => $stripe_plan_id,
                    ],
                ],
                'prorate' => false,
                'metadata' => ['plan_id'  =>  $plan->id]
            ]);

            if($is_downgrade) {
                alert()->success(__('Your subscription will be updated at the end of the billing period'));
                $user->new_membership_plan = $plan->id;
            } else {
                alert()->success(__('Successfully changed subscription'));
                $user->newSubscription('main', $plan)->create();
                unset($user->new_membership_plan);
            }
        } else {
            if($subscription->status != "canceled") {
                $subscription->cancel();
            }
            $user->new_membership_plan = $plan->id;
            alert()->success(__('Your subscription will be cancelled at the end of the billing period.'));
        }
        $user->save();

        return redirect()->route('addons.memberships.subscribe');
    }

    private function getPlan($plan_id)
    {
        try {
            return \Stripe\Plan::retrieve($plan_id);
        } catch (\Exception $e) {

        }
        return false;
    }

    public function createOrUpdatePlan($plan) {

        $params = [
            'id' => $plan->id,
            'amount' => $plan->price * 100,
            'interval' => $plan->interval,
            'currency' => setting("currency"),
        ];
        $plan_key = implode("-", array_values($params));
        $stripe_plan_id = str_slug("marketplacekit-".setting('site_title'))."-".$plan_key;
        $stripe_plan = $this->getPlan($stripe_plan_id);
        #dd($stripe_plan);
        if(!$stripe_plan) {
            \Stripe\Plan::create(array(
                "amount" => $plan->price * 100,
                "interval" => $plan->interval,
                "product" => array(
                    "name" => $plan->name
                ),
                "currency" => setting("currency"),
                "id" => $stripe_plan_id
            ));
        }

        return $stripe_plan_id;
    }

    private function createOrUpdateCustomer($user, $token) {

        if(!$user->membership_stripe_customer) {
            $customer = \Stripe\Customer::create(array(
                'email' => auth()->user()->email,
                'source' => $token,
            ));
            $user->membership_stripe_customer = $customer->id;
            $user->save();
        } else {

            $customer = \Stripe\Customer::retrieve($user->membership_stripe_customer);
            $customer->source = $token;
            $customer->save();

        }

        return $customer;
    }


    public function webhook(Request $request) {
        \Log::debug(json_encode($request->all()));
        if($request->input('type') == "invoice.payment_succeeded") {
            $sub_id = $request->input('data.object.subscription');
            $new_plan_id = $request->input('data.object.metadata.plan_id');
            $period_end = Carbon::createFromTimestamp($request->input('data.object.period_end'));
            $user_subscription = User::getModelStub()
                                    ->where('key', 'membership_stripe_subscription')
                                    ->where('value', $sub_id)
                                    ->first();
            if($user_subscription) {
                $user = User::find($user_subscription->user_id);
                #renew if end date is greater than the one stored
                if($user->subscription('main')->ends_at->diffInDays($period_end, false) > 0) {
                    $user->subscription('main')->renew();
                }

                if($new_plan_id == $user->subscription('main')->plan->id) {
                    $user->subscription('main')->renew();
                } else {
                    $new_plan = Plan::find($new_plan_id);
                    $user->newSubscription('main', $new_plan)->create();
                    unset($user->new_membership_plan);
                    $user->save();
                }

            }
        }

        return ['status' => true, 'data' => $request->input('type')];
    }

}
