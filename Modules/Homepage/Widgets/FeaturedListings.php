<?php

namespace Modules\Homepage\Widgets;

use Arrilot\Widgets\AbstractWidget;
use App\Models\Listing;

class FeaturedListings extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    public function __construct(array $config = [])
    {
        $this->addConfigDefaults([
            'limit' => 4
        ]);

        parent::__construct($config);
    }

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run($widget)
    {
        //
        $this->config['limit'] = ($this->config['limit'] > 12)?12:$this->config['limit'];
        $items = Listing::limit($this->config['limit'])->active()->orderBy('spotlight', 'DESC')->where('title', '!=', '')
                    ->get();

        return view('home.widgets.featured_listings', [
            'config' => $this->config,
            'items' => $items,
            'widget' => $widget,
        ]);
    }
}
