<?php

namespace Modules\Sitemap\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\PageTranslation;
use Location;
use Setting;
use Sitemap;
use MetaTag;
use App;
use URL;

class SitemapController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $sitemap = App::make('sitemap');
		
        $sitemap->setCache('laravel.sitemap', 0, 60); #in minutes
		
		// check if there is cached sitemap and build new only if is not
        if (!$sitemap->isCached() || true) {
		
            // add item to the sitemap (url, date, priority, freq)
            $sitemap->add(URL::to('/'), null, '1.0', 'daily');
			
			if (setting('custom_homepage') || module_enabled('homepage')) {
				$sitemap->add(route(setting('marketplace_index')), null, '0.9', 'daily');
			}
			

            foreach( menu() as $item ) {
                $sitemap->add(URL::to($item['url']), null, '0.9', 'daily');
            }
			$pages = PageTranslation::get();
			
            foreach( $pages as $item ) {
				if($item->published_at) {
					$sitemap->add(route('page', $item->slug), null, '0.9', 'daily');
				}
            }
			
            $listings = Listing::active()->orderBy('created_at', 'DESC')->limit(50)->get();
            foreach($listings as $listing) {
                $sitemap->add($listing->url, null, '0.9', 'daily');
            }
			
        }

        return $sitemap->render('xml');
    }

}
