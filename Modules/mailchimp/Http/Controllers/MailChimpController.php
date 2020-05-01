<?php

namespace Modules\MailChimp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Kris\LaravelFormBuilder\FormBuilder;
use Setting;
use Newsletter;
use Crypt;

class MailChimpController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(FormBuilder $formBuilder)
    {
        $settings = Setting::all();
        try {
            $settings['mailchimp_api_key'] = Crypt::decryptString($settings['mailchimp_api_key']);
        } catch (\Exception $e) {

        }
        $form = $formBuilder->create('Modules\MailChimp\Forms\MailChimpForm', [
            'method' => 'POST',
            'url' => route('panel.addons.mailchimp.store'),
        ], $settings);

        $mailchimp_url = null;
        if(setting('mailchimp_list_id')) {
            $mailchimp_url = "https://us18.admin.mailchimp.com/lists/";
        }

        return view('mailchimp::index', compact('form', 'mailchimp_url'));
    }
    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        config(['newsletter.apiKey' => $request->input('mailchimp_api_key')]);
        config(['newsletter.lists.subscribers.id' => $request->input('mailchimp_list_id')]);
        $api = null;
        try {
            $api = Newsletter::getMembers();
        } catch (\Exception $e) {
            alert()->danger($e->getMessage());
            return redirect()->route('panel.addons.mailchimp.index');
        }

        $api = Newsletter::getMembers();
        if(isset($api['status'])) {
            alert()->danger('Oops! something went wrong. Please check your api key or list id.');
            return redirect()->route('panel.addons.mailchimp.index');
        }

        Setting::set('mailchimp_api_key', Crypt::encryptString($request->input('mailchimp_api_key')));
        Setting::set('mailchimp_list_id', $request->input('mailchimp_list_id'));
        Setting::save();

        alert()->success('Successfully saved');
        return redirect()->route('panel.addons.mailchimp.index');
    }

}
