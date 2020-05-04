<?php

namespace Modules\Ratings\Forms;

use Kris\LaravelFormBuilder\Form;

class RatingsForm extends Form
{
    public function buildForm()
    {

        $this->add('rating_permission_group', 'select', [
            'choices' => ['members' => 'Members Only', 'buyers' => 'Buyers Only'],
            'label' => "Who can submit reviews?",
            'selected' => 'members',
            'empty_value' => '=== Select ===',
            'default_value' => $this->getData('rating_permission_group')
        ]);
        $this->add('rating_profile_page', 'checkbox', [
            'label' => "Show reviews on profile page",
            'value' => 1,
            'checked' => (bool) $this->getData('rating_profile_page', 1)
        ]);
        $this->add('rating_profile_limit', 'number', [
            'label' => "Number of reviews to show on profile page",
            'default_value' => $this->getData('rating_profile_limit')
        ]);
        $this->add('rating_listing_page', 'checkbox', [
            'label' => "Show reviews on listings page",
            'value' => 1,
            'checked' => (bool) $this->getData('rating_listing_page', 1)
        ]);
        $this->add('rating_listing_limit', 'number', [
            'label' => "Number of reviews to show on listings page",
            'default_value' => $this->getData('rating_listing_limit')
        ]);
        $this->add('submit', 'submit', ['attr' => ['class' => 'btn btn-primary']]);

    }
}
