<?php

namespace Modules\Memberships\Forms;

use Kris\LaravelFormBuilder\Form;

class MembershipPlanForm extends Form
{
    public function buildForm()
    {
        $this->add('name', 'text', [
            'default_value' => $this->getData('name'),
            'rules' => 'required',
        ]);
        $this->add('description', 'text', [
            'default_value' => $this->getData('description')
        ]);
        $this->add('price', 'text', [
            'label' => 'Price per month',
            'default_value' => $this->getData('price'),
            'rules' => 'required',
        ]);
        $this->add('listings', 'number', [
            'default_value' => $this->getData('listings', 1),
            'label' => 'Number of listings/month',
            'rules' => 'required|numeric',
        ]);
        $this->add('images', 'select', [
            'default_value' => $this->getData('images', 1),
            'label' => 'Number of photos per listing',
            'rules' => 'required|numeric',
            'choices' => array_combine(range(1, 20),range(1, 20)),
            'empty_value' => '-- SELECT --'
        ]);
        $this->add('featured_listings', 'number', [
            'default_value' => $this->getData('featured_listings', 0),
            'label' => 'No. of Featured Listings',
            'rules' => 'required|numeric',
        ]);
        $this->add('bold_listings', 'number', [
            'default_value' => $this->getData('bold_listings', 0),
            'label' => 'No. of Bold Listings',
            'rules' => 'required|numeric',
        ]);
        $this->add('priority_listings', 'number', [
            'default_value' => $this->getData('bold_listings', 0),
            'label' => 'No. of Priority Listings',
            'rules' => 'required|numeric',
        ]);
        $this->add('messages', 'number', [
            'default_value' => $this->getData('messages', 3),
            'label' => 'Number of messages a user can send per month',
            'rules' => 'required|numeric',
        ]);
        $this->add('credits', 'number', [
            'default_value' => $this->getData('credits', 3),
            'label' => 'Enter the number of credits this plan contains',
            'rules' => 'required|numeric',
            'help_block' => ['text' => "Only use this if you want to use credit based billing", 'attr' => ['class' => 'text-muted small mb-0 pb-0 help-block']],
        ]);
        $this->add('Submit', 'submit', ['attr' => ['class' => 'btn btn-primary']]);

    }
}
