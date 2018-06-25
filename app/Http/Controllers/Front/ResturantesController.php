<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Pagination\LengthAwarePaginator;
use DB;
use App\Models\Resturant;
use App\Models\MenuSection;
use App\Models\Meal;

class ResturantesController extends FrontController {

    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request) {

        if (\Cookie::get('city_id') !== null && \Cookie::get('area_id') !== null) {
            $city_id = \Cookie::get('city_id');
            $area_id = \Cookie::get('area_id');
        } else {
            $city_id = '';
            $area_id = '';
        }
        $where_array['city_id'] = $city_id;
        $where_array['region_id'] = $area_id;
        $this->data['resturantes'] = $this->getResturantes($where_array, 'transformOnePagination');
        //dd( $this->data['resturantes']);
        return $this->_view('resturantes.index');
    }

    public function getResturantesByCuisine(Request $request, $slug) {
        $this->data['resturantes'] = $this->getResturantes(array('cuisine_slug'=>$slug), 'transformOnePagination');
        return $this->_view('resturantes.index');
        return $this->_view('resturantes.index');

    }

    public function resturant(Request $request, $slug) {
        $resturant = $this->getResturantes(array('slug' => $slug), 'transformOneDetails');
        //dd($resturant);
        if (!$resturant) {
            return $this->err404();
        }
        $this->data['resturant'] = $resturant;
        return $this->_view('resturantes.view');
    }

    public function menu(Request $request, $resturant, $menu) {

        $meals = $this->getMeals(array('resturant' => $resturant, 'menu_section' => $menu), 'transformForPagination');
        $this->data['meals'] = $meals;
        return $this->_view('resturantes.meals');
    }

    public function meal(Request $request, $resturant, $menu, $meal) {
        $meals = $this->getMeals(array('resturant' => $resturant, 'menu_section' => $menu, 'meal' => $meal), 'transformForDetails');
        $this->data['meal'] = $meals;
//        dd($this->data['meal']);
        return $this->_view('resturantes.meal');
    }

    private function getResturantes($where_array, $transform_type = "transformOnePagination") {
        $resturantes = Resturant::join('resturant_branches', 'resturantes.id', '=', 'resturant_branches.resturant_id');
        $resturantes->join('resturant_cuisines', 'resturantes.id', '=', 'resturant_cuisines.resturant_id');
        $resturantes->join('cuisines', 'cuisines.id', '=', 'resturant_cuisines.cuisine_id');
        $resturantes->join('cities as region', 'region.id', '=', 'resturant_branches.region_id');
        if (isset($where_array['city_id'])) {
            $resturantes->where('resturant_branches.city_id', $where_array['city_id']);
        }
        if (isset($where_array['region_id'])) {
            $resturantes->where('resturant_branches.region_id', $where_array['region_id']);
        }
        if (isset($where_array['category_id'])) {
            $resturantes->where('resturantes.category_id', $where_array['category_id']);
        }
        if (isset($where_array['slug'])) {
            $resturantes->where('resturant_branches.slug', $where_array['slug']);
        }
        if (isset($where_array['cuisine_slug'])) {
            $resturantes->where('cuisines.slug', $where_array['cuisine_slug']);
        }
        if (isset($where_array['query'])) {
            $resturantes->whereRaw(handleKeywordWhere(['resturantes.title_ar', 'resturantes.title_en'], $where_array['query']));
        }
        $resturantes->where('resturantes.available', 1);
        $resturantes->where('resturantes.active', 1);
        $resturantes->groupBy('resturantes.id');
        $resturantes->select("resturantes.*", "region.title_$this->lang_code as region_title", "resturant_branches.title_$this->lang_code as title", "resturant_branches.slug", 'resturant_branches.delivery_cost', DB::raw("(SELECT Count(*) FROM offers WHERE resturant_id = resturantes.id and active = 1 and available_until > " . date('Y-m-d') . ") as offers"));
        if ($transform_type == "transformOnePagination") {
            $resturantes = $resturantes->paginate($this->limit);
            $resturantes->getCollection()->transform(function($resturant, $key) use($transform_type) {
                return Resturant::$transform_type($resturant);
            });
        } else {
            $resturantes = $resturantes->first();
            if ($resturantes) {
                $resturantes = Resturant::$transform_type($resturantes);
            }
        }


        return $resturantes;
    }

    private function getMeals($where_array, $transform_type = "transformForPagination") {
        $user = $this->User;
        $columns = array('meals.*', 'resturantes.id as resturant_id', 'resturant_branches.slug as resturant_slug', 'menu_sections.slug as menu_section_slug',
            'resturantes.service_charge', 'resturantes.vat', 'resturant_branches.delivery_cost', 'resturant_branches.id as resturant_branch_id');
        $meals = Meal::join('menu_sections', 'menu_sections.id', '=', 'meals.menu_section_id');
        $meals->join('resturantes', 'resturantes.id', '=', 'menu_sections.resturant_id');
        $meals->join('resturant_branches', 'resturantes.id', '=', 'resturant_branches.resturant_id');
        if ($this->User) {
            $columns[] = 'favourites.id as favourite_id';
            $meals->leftJoin('favourites', function ($join) use($user) {
                $join->on('favourites.meal_id', '=', 'meals.id')
                        ->where('favourites.user_id', '=', $user->id);
            });
        }
        $meals->where('resturant_branches.slug', $where_array['resturant']);
        $meals->where('menu_sections.slug', $where_array['menu_section']);
        if (isset($where_array['meal'])) {
            $meals->where('meals.slug', $where_array['meal']);
        }
        $meals->where('meals.active', 1);
        $meals->groupBy('meals.id');
        $meals->select($columns);
        if ($transform_type == "transformForPagination") {
            $meals = $meals->paginate($this->limit);
            $meals->getCollection()->transform(function($meal, $key) use($transform_type) {
                return Meal::$transform_type($meal);
            });
        } else {
            $meals = $meals->first();
            if ($meals) {
                $meals = Meal::$transform_type($meals);
            }
        }


        return $meals;
    }

}
