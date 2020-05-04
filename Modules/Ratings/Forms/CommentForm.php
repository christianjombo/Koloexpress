<?php

namespace Modules\Ratings\Forms;

use Kris\LaravelFormBuilder\Form;

class CommentForm extends Form
{
    public function buildForm()
    {

        $this->add('approved', 'select', [
            'choices' => [0 => 'Hidden', 1 => 'Approved'],
            'label' => "Comment status",
            'default_value' => $this->getData('approved')
        ]);
        $this->add('comment', 'textarea', [
            'label' => "Comment",
            'attr' => ['style' => 'height: 100px'],
            'default_value' => $this->getData('comment')
        ]);
        $this->add('rate', 'number', [
            'label' => "Rating",
            'default_value' => $this->getData('rate')
        ]);
        $this->add('submit', 'submit', ['attr' => ['class' => 'btn btn-primary']]);

    }
}
