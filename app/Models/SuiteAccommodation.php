<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class SuiteAccommodation extends MyModel {

    protected $table = 'suites_accommodation';
    public static $types = [
        'suite_lounge_seat',
        'suite_lounge_chair',
        'suite_lounge_bed',
        'suite_lounge',
    ];

    public static function gePilgrimsWithAccommodation($where_array = array(), $id = false) {
        $pilgrims = Pilgrim::join('locations', 'locations.id', '=', 'pilgrims.location_id');
        $pilgrims->join('locations_translations', 'locations.id', '=', 'locations_translations.location_id');
        $pilgrims->join('pilgrims_class', 'pilgrims_class.id', '=', 'pilgrims.pilgrim_class_id');
        $pilgrims->join('pilgrims_class_translations', 'pilgrims_class.id', '=', 'pilgrims_class_translations.pilgrims_class_id');
        $pilgrims->join('suites_accommodation', 'pilgrims.id', '=', 'suites_accommodation.pilgrim_id');
        $pilgrims->join('lounges', 'lounges.id', '=', 'suites_accommodation.lounge_id');
        $pilgrims->join('suites', 'suites.id', '=', 'lounges.suite_id');
        if (isset($where_array['filter'])) {
            $pilgrims->select(['suites_accommodation.id', "pilgrims.reservation_no", "pilgrims.gender", "pilgrims.mobile", "pilgrims.image", "pilgrims.name",
                "pilgrims_class_translations.title as pilgrim_class_title", "pilgrims.ssn", "pilgrims.name", "pilgrims.code", "pilgrims.mobile", "suites_accommodation.id as suites_accommodation_id",
                "suites.number as suite_number", "lounges.number as lounge_number", "suites_accommodation.number as lounge_seat_number", "locations_translations.title as location_title"]);
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
            $pilgrims->orderBy('suites.number', 'ASC');
            $pilgrims->orderBy('lounges.number', 'ASC');
            $pilgrims->orderBy('suites_accommodation.number', 'ASC');
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
            if (isset($where_array['filter']['reservation_no'])) {
                $pilgrims->where("pilgrims.reservation_no", $where_array['filter']['reservation_no']);
            }

            //destroy
        } else if (isset($where_array['destroy'])) {
            if (isset($where_array['destroy']['ids'])) {
                $pilgrims->whereIn("suites_accommodation.id", $where_array['destroy']['ids']);
            }
        }
        return $pilgrims;
    }

}
