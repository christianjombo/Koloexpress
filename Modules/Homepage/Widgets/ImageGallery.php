<?php

namespace Modules\Homepage\Widgets;

use Arrilot\Widgets\AbstractWidget;
use App\Models\Category;

class ImageGallery extends AbstractWidget
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
        $categories = [
            8, 4, 4, 4, 4
        ];
        return view('home.widgets.image_gallery', [
            'config' => $this->config,
            'categories' => $categories,
            'widget' => $widget,
        ]);
    }
}
