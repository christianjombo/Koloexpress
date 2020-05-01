<?php

namespace Modules\MailChimp\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Newsletter;
use HumanNameParser\Parser;

class SubscribeToNewsletter
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
        try {

            if(setting('mailchimp_api_key')) {
                $nameparser = new Parser();
                $name = $nameparser->parse($event->user->name);

                Newsletter::subscribeOrUpdate($event->user->email, ['FNAME' => $name->getFirstName(), 'LNAME' => $name->getLastName()]);
            }

        } catch (\Exception $e) {
            #log file
        }

    }
}
