<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use App\Models\Slider;
use App\Models\Ad;
use App\Models\Offer;
use App\Models\Category;
use App\Models\City;
use App\Models\Resturant;
use App\Models\Cuisine;

class HomeController extends FrontController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
//        $ipAddress ='156.218.107.118';
//  
//
//    $position = \Location::get( $ipAddress );
//    
//    dd($position);
//    dd(getAddress($position->latitude, $position->longitude));
//     dd(getAddress('29.99924','31.19426'));
        $this->data['slider'] = $this->getSlider();
        $this->data['ads'] = $this->getAds();
        $this->data['offers'] = $this->getOffers();
        $this->data['cities'] = $this->getCities();
        $this->data['categories'] = $this->getCategories();
      
        $this->data['famous_resturants'] = $this->getFamousResturants();
        //dd($this->data['famous']);
        return $this->_view('index');
    }

    private function getSlider() {
        $slider = Slider::where('active', 1)->orderBy('this_order', 'ASC')->get();
        return Slider::transformCollection($slider);
    }

    private function getFamousResturants() {
        $Resturants = Resturant::join('resturant_branches', 'resturantes.id', '=', 'resturant_branches.resturant_id')
                ->where('resturantes.active', 1)
                ->where('resturantes.available', 1)
                ->where('resturantes.is_famous', 1)
                ->select(["resturantes.title_$this->lang_code  as title","resturantes.options","resturantes.image", "resturant_branches.slug",])
                ->groupBy('resturantes.id')
                ->take(4)
                ->get();
        return Resturant::transformCollection($Resturants,"Famous");
    }
    


    
    private function getAds() {
        $ads = Ad::join('resturantes', 'resturantes.id', '=', 'ads.resturant_id')
                ->where('ads.active', 1)
                ->orderBy('ads.this_order', 'ASC')
                ->select(["ads.ad_image as image","ads.url"])
                ->get();
        return Ad::transformCollection($ads,"Home");
    }
    private function getOffers() {
        
        $offers = Offer::join('resturantes', 'resturantes.id', '=', 'offers.resturant_id')
                ->join('resturant_branches', 'resturantes.id', '=', 'resturant_branches.resturant_id')
                ->where('offers.available_until','>', date('Y-m-d'))
                ->orderBy('offers.this_order', 'ASC')
                ->select(["offers.image as image", "resturantes.title_$this->lang_code as resturant_title",
                    "resturant_branches.slug as resturant_slug"])
                ->groupBy('resturantes.id')
                ->get();
        return Offer::transformCollection($offers,"Home");
    }
    private function getCategories() {
        
        $categories = Category::join('resturantes', 'categories.id', '=', 'resturantes.category_id')
                ->groupBy('categories.id')
                ->select(["categories.id", "categories.title_$this->lang_code as title"])
                ->get();
        return $categories;
    }
  
    private function getCities() {
        
        $cities = City::join('resturant_branches', 'cities.id', '=', 'resturant_branches.city_id')
                ->join('resturantes', 'resturantes.id', '=', 'resturant_branches.resturant_id')
                ->groupBy('cities.id')
                ->select(["cities.id", "cities.title_$this->lang_code as title"])
                ->get();
        return $cities;
    }

}
