<?php

namespace Modules\Homepage\Widgets;

use Arrilot\Widgets\AbstractWidget;

class Hero extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run($widget)
    {
        //
        $data = [
            'config' => $this->config,
            'widget' => $widget
        ];
        if(view()->exists('home.widgets.hero')){
            return view('home.widgets.hero', $data);
        }
        return view('homepage::widgets.hero', $data);

    }
}
