<?php

namespace Modules\Ratings\Widgets;

use App\Models\Listing;
use Arrilot\Widgets\AbstractWidget;

class ListingReviews extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [
        'limit' => 5,
        'listing' => 0,
    ];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        //
        $listing = Listing::find($this->config['listing']);
        $data = [];
        $data['config'] = $this->config;
        $data['profile'] = $listing->user;
        $data['listing'] = $listing;
        $data['comments'] = $listing->comments()->orderBy('created_at', 'DESC')->limit($this->config['listing'])->get();
        $data['comment_count'] = $listing->totalCommentCount();

        if(view()->exists('widgets.reviews')){
            return view('widgets.reviews', $data);
        }
        return view('ratings::widgets.reviews', $data);
    }
}
