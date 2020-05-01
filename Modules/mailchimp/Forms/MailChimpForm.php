<?php

namespace Modules\MailChimp\Forms;

use Kris\LaravelFormBuilder\Form;

class MailChimpForm extends Form
{
    public function buildForm()
    {

        $this->add('mailchimp_api_key', 'text', [
            'label' => "MailChimp API key",
            'default_value' => $this->getData('mailchimp_api_key'),
            'help_block' => [
                'text' => "The API key of a MailChimp account. You can find yours at <a href='https://us10.admin.mailchimp.com/account/api-key-popup/' target='_blank'>https://us10.admin.mailchimp.com/account/api-key-popup/</a>.",
                'tag' => 'p',
                'attr' => ['class' => 'help-block']
            ],
        ]);

        $this->add('mailchimp_list_id', 'text', [
            'label' => "MailChimp List ID",
            'default_value' => $this->getData('mailchimp_list_id'),
            'help_block' => [
                'text' => "A MailChimp list id. Check the <a href='http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id' target='_blank'>MailChimp docs</a> if you don't know how to get this value.",
                'tag' => 'p',
                'attr' => ['class' => 'help-block']
            ],
        ]);

        $this->add('submit', 'submit', ['attr' => ['class' => 'btn btn-primary']]);

    }
}
