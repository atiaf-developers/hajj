<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class TentAccommodation extends MyModel {

    protected $table = 'tents_accommodation';


    public static function gePilgrimsWithAccommodation($where_array = array(), $id = false) {
        $pilgrims = Pilgrim::join('locations', 'locations.id', '=', 'pilgrims.location_id');
        $pilgrims->join('locations_translations', 'locations.id', '=', 'locations_translations.location_id');
        $pilgrims->join('pilgrims_class', 'pilgrims_class.id', '=', 'pilgrims.pilgrim_class_id');
        $pilgrims->join('pilgrims_class_translations', 'pilgrims_class.id', '=', 'pilgrims_class_translations.pilgrims_class_id');
        $pilgrims->join('tents_accommodation', 'pilgrims.id', '=', 'tents_accommodation.pilgrim_id');
        $pilgrims->join('tents', 'tents.id', '=', 'tents_accommodation.tent_id');
        if (isset($where_array['filter'])) {
            $pilgrims->select(['tents_accommodation.id', "pilgrims.reservation_no", "pilgrims.gender", "pilgrims.mobile", "pilgrims.image", "pilgrims.name",
                "pilgrims_class_translations.title as pilgrim_class_title", "pilgrims.ssn", "pilgrims.name", "pilgrims.code", "pilgrims.mobile", "tents_accommodation.id as tents_accommodation_id",
                "tents.number as tent_number", "locations_translations.title as location_title"]);
        } 

        $pilgrims->where('pilgrims_class_translations.locale', static::getLangCode());
        $pilgrims->where('locations_translations.locale', static::getLangCode());


        if ($id) {
            $pilgrims->where('pilgrims.id', $id);
            $pilgrims = $pilgrims->first();
            if ($pilgrims) {
                $pilgrims = static::transform($pilgrims);
            }
        } else {
            $pilgrims->orderBy('tents.number', 'ASC');
            $pilgrims = static::handleWhere($pilgrims, $where_array);
            $limit = isset($where_array['filter']['per_page']) ? $where_array['filter']['per_page'] : static::$limit;
            if (isset($where_array['filter'])) {
                $pilgrims = $pilgrims->paginate($limit)->appends($where_array['filter']);
            }  else {
                $pilgrims = $pilgrims->get();
            }
        }

        return $pilgrims;
    }

    private static function handleWhere($pilgrims, $where_array) {

        if (isset($where_array['filter'])) {
            //filter
            if (isset($where_array['filter']['location'])) {
                $pilgrims->where("locations.id", $where_array['filter']['location']);
            }
            if (isset($where_array['filter']['pilgrim_class'])) {
                $pilgrims->where("pilgrims_class.id", $where_array['filter']['pilgrim_class']);
            }
            if (isset($where_array['filter']['gender'])) {
                $pilgrims->where("pilgrims.gender", $where_array['filter']['gender']);
            }
            if (isset($where_array['filter']['code'])) {
                $pilgrims->where("pilgrims.code", $where_array['filter']['code']);
            }
            if (isset($where_array['filter']['ssn'])) {
                $pilgrims->where("pilgrims.ssn", $where_array['filter']['ssn']);
            }
            if (isset($where_array['filter']['reservation_no'])) {
                $pilgrims->where("pilgrims.reservation_no", $where_array['filter']['reservation_no']);
            }

            //destroy
        } else if (isset($where_array['destroy'])) {
            if (isset($where_array['destroy']['ids'])) {
                $pilgrims->whereIn("tents_accommodation.id", $where_array['destroy']['ids']);
            }
        }
        return $pilgrims;
    }

}
