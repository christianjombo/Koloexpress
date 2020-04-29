<?php

namespace Modules\Homepage\Widgets;

use Arrilot\Widgets\AbstractWidget;

class Paragraph extends AbstractWidget
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
        $items = [
            ['title' => 'For garden owners', 'icon' => 'store', 'button_text' => 'Browse gardeners', 'button_link' => ''],
            ['title' => 'For gardening profesionals', 'icon' => 'coin', 'button_text' => 'Browse gardeners', 'button_link' => ''],
            ['title' => 'For gardening profesionals', 'icon' => 'coin', 'button_text' => '', 'button_link' => ''],
        ];
        $items = json_decode(json_encode($items), FALSE);

        return view('home.widgets.paragraph', [
            'config' => $this->config,
            'items' => $items,
            'widget' => $widget,
        ]);
    }
}
