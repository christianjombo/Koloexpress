<?php

namespace Modules\Ratings\Widgets;

use App\Models\Listing;
use App\Models\User;
use Arrilot\Widgets\AbstractWidget;

class ProfileReviews extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [
        'limit' => 5,
        'user' => 0,
    ];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        //
        $user = User::find($this->config['user']);
        $data = [];
        $data['profile'] = $user;
        $data['comments'] = $user->comments()->with('commenter')->orderBy('created_at', 'DESC')->limit(5)->get();
        $data['rating'] = $user->averageRate();

        if(view()->exists('widgets.profile_reviews')){
            return view('widgets.profile_reviews', $data);
        }
        return view('ratings::widgets.profile_reviews', $data);
    }
}
