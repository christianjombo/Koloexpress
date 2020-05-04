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


use PayPal\Api\Agreement;
use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Api\Payer;
use PayPal\Api\PaymentDefinition;

class PaypalController extends SubscribeController
{

    public function redirect(Request $request) {
        $plan_id = $request->input('plan_id');
        $plan = Plan::find($plan_id);

        $dt = Carbon::now();
        $user = auth()->user();
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                setting('paypal_client_id'),     // ClientID
                setting('paypal_client_secret')      // ClientSecret
            )
        );

        $paypal_plan = new \PayPal\Api\Plan();
        $paypal_plan->setName($plan->name)
            ->setDescription(setting('site_title'). " - " .$plan->description)
            ->setType('INFINITE');

        $merchantPreferences = new MerchantPreferences();
        $merchantPreferences->setReturnUrl(route('addons.memberships.paypal.return'))
            ->setCancelUrl(route('addons.memberships.paypal.cancel'))
            ->setAutoBillAmount("yes")
            ->setInitialFailAmountAction("CONTINUE")
            ->setMaxFailAttempts("0")
            ->setSetupFee(new Currency(array('value' => $plan->price, 'currency' => setting("currency"))));
        $paypal_plan->setMerchantPreferences($merchantPreferences);

        $paymentDefinition = new PaymentDefinition();
        $paymentDefinition->setName(setting('site_title').' Payments')
            ->setType('REGULAR')
            ->setFrequency('Month')
            ->setFrequencyInterval("1")
            ->setCycles("0")
            ->setAmount(new Currency(array('value' => $plan->price, 'currency' => setting("currency"))));
        $paypal_plan->setPaymentDefinitions(array($paymentDefinition));
        try {
            $output = $paypal_plan->create($apiContext);
        } catch (\Exception $e) {
            dd($e);
        }

        // Activate the plan
        $patch_request = new PatchRequest();
        $patch = new Patch();
        $patch->setOp('replace');
        $patch->setPath('/');
        $patch->setValue(["state" => "ACTIVE"]);
        $patches = array();
        $patches[] = $patch;

        $patch_request->setPatches($patches);
        $result = $paypal_plan->update($patch_request, $apiContext);

        //set-up the agreement
        $dt = Carbon::now();
        $agreement = new Agreement();
        $agreement->setName($plan->name)
            ->setDescription('Basic Agreement')
            ->setStartDate($dt->addMonth()->toIso8601String());

        // Add Plan ID
        $agreement_plan = new \PayPal\Api\Plan();
        $agreement_plan->setId($paypal_plan->getId());
        $agreement->setPlan($agreement_plan);

        // Add Payer
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $agreement->setPayer($payer);

        #hate this too!
        session(['addons.memberships.paypal.plan_id', $plan->id]);

        ### Create Agreement
        try {
            // Please note that as the agreement has not yet activated, we wont be receiving the ID just yet.
            $agreement = $agreement->create($apiContext);
            $approvalUrl = $agreement->getApprovalLink();
            return redirect($approvalUrl);
        } catch (\Exception $e) {
            dd($e);
        }
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

    public function createOrUpdateCustomer($user, $token) {

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

    public function cancel_url(Request $request) {
        return redirect()->route('addons.memberships.subscribe');
    }

    public function return_url(Request $request) {

        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                setting('paypal_client_id'),     // ClientID
                setting('paypal_client_secret')      // ClientSecret
            )
        );
        $agreement = new \PayPal\Api\Agreement();
        try {
            // ## Execute Agreement
            // Execute the agreement by passing in the token
            $result = $agreement->execute($request->input('token'), $apiContext);

            $user = auth()->user();
            $user->membership_paypal_subscription = $result->id;
            $user->save();

            $plan = Plan::find(session('addons.memberships.paypal.plan_id'));
            $user->newSubscription('main', $plan)->create();

            #store the payment
            #$this->storePayment($user, $plan);

            #check if this customer has an existing plan. If so cancel plan and re-sub.
            alert()->success(__('Successfully subscribed'));
            return redirect()->route('addons.memberships.subscribe');

        } catch (\Exception $e) {
            dd($e);
        }

    }

    public function switch_plan(Request $request) {

        \Stripe\Stripe::setApiKey(config('marketplace.stripe_secret_key'));

        $new_plan = $request->input('new_plan');

        $plan = Plan::find($request->input('new_plan'));
        $user = auth()->user();
        $subscription = \Stripe\Subscription::retrieve($user->membership_stripe_subscription);

        if($plan->price > 0) {
            $stripe_plan_id = $this->createOrUpdatePlan($plan);
            \Stripe\Subscription::update($user->membership_stripe_subscription, [
                'cancel_at_period_end' => false,
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'plan' => $stripe_plan_id,
                    ],
                ],
            ]);

            $user->newSubscription('main', $plan)->create();

        } else {
            $subscription->update(['cancel_at_period_end' => true]);
            $user->subscription('main')->cancel();

            $subscription->cancel();
            $user->newSubscription('main', $plan)->create();
            $user->membership_stripe_subscription = null;
            $user->save();
        }

        alert()->success(__('Successfully changed subscription'));
        return redirect()->route('addons.memberships.subscribe');
    }

}
