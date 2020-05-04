<?php

namespace Modules\Memberships\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Kris\LaravelFormBuilder\FormBuilder;
use Gerardojbaez\Laraplans\Models\Plan;
use Gerardojbaez\Laraplans\Models\PlanFeature;

class MembershipsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        //show list of current plans
        $plans = Plan::get();
        return view('memberships::admin.index', compact('plans'));
    }

    public function subscriptions()
    {
        //show list of current plans

        #dd($subscriptions);
        return view('memberships::admin.subscriptions', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create('Modules\Memberships\Forms\MembershipPlanForm', [
            'method' => 'POST',
            'url' => route('panel.addons.memberships.store'),
        ]);
        $data['form'] = $form;
        return view('memberships::admin.create', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        $plan = Plan::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'interval' => 'month',
            'interval_count' => 1,
            'trial_period_days' => 0,
            'sort_order' => $request->input('sort_order'),
        ]);

        $plan->features()->saveMany([
            new PlanFeature(['code' => 'listings', 'value' => $request->input('listings'), 'sort_order' => 1]),
            new PlanFeature(['code' => 'images', 'value' => $request->input('images'), 'sort_order' => 5]),
            new PlanFeature(['code' => 'featured_listings', 'value' => $request->input('featured_listings'), 'sort_order' => 15]),
            new PlanFeature(['code' => 'messages', 'value' => $request->input('messages'), 'sort_order' => 20]),
            new PlanFeature(['code' => 'bold_listings', 'value' => $request->input('bold_listings'), 'sort_order' => 25]),
            new PlanFeature(['code' => 'priority_listings', 'value' => $request->input('priority_listings'), 'sort_order' => 25]),
        ]);

        alert()->success('Successfully saved');
        return redirect()->route('panel.addons.memberships.index');
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('memberships::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id, FormBuilder $formBuilder)
    {
        $plan = Plan::findOrFail($id);
        $params = $plan->toArray() + collect($plan->features->toArray())->pluck('value', 'code')->toArray();

        $form = $formBuilder->create('Modules\Memberships\Forms\MembershipPlanForm', [
            'method' => 'PUT',
            'url' => route('panel.addons.memberships.update', $id),
        ], $params);

        $data['form'] = $form;
        return view('memberships::admin.create', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $plan = Plan::findOrFail($id);
        $plan->fill($request->all());
        $plan->save();

        foreach(['listings', 'images', 'bold_listings', 'featured_listings', 'priority_listings', 'messages'] as $input){
            $feature = PlanFeature::where('code', $input)->where('plan_id', $plan->id)->first();
            if($feature) {
                $feature->value = $request->input($input, 0);
                $feature->save();
            }
        }

        alert()->success('Successfully saved');
        return redirect()->route('panel.addons.memberships.index');
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();

        alert()->success('Successfully deleted');
        return redirect()->route('panel.addons.memberships.index');
    }
}
