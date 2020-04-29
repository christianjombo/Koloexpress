<?php

namespace Modules\Homepage\Widgets;

use Arrilot\Widgets\AbstractWidget;

class Video extends AbstractWidget
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

        return view('home.widgets.video', [
            'config' => $this->config,
            'widget' => $widget,
        ]);
    }
}
