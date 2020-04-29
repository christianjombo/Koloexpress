<?php

namespace Modules\Homepage\Widgets;

use Arrilot\Widgets\AbstractWidget;
use App\Models\Category;

class CategoryListing extends AbstractWidget
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
        //first two levels
        $categories = Category::where('parent_id', 0)->get();
        foreach($categories as $i => $category) {
            $child_categories = Category::where('parent_id', $category->id)->get();
            $categories[$i]->categories = $child_categories;
        }
#dd($categories);
        return view('home.widgets.category_listing', [
            'config' => $this->config,
            'categories' => $categories,
            'widget' => $widget,
        ]);
    }
}
