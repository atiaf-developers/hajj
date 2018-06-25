<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class BuildingAccommodation extends MyModel {

    protected $table = 'buildings_accommodation';
    public static $types = [
        'building_room_floor_1',
        'building_room_floor_2',
        'building_room'
    ];

    public static function gePilgrimsWithAccommodation($where_array = array(), $id = false) {
        $pilgrims = Pilgrim::join('locations', 'locations.id', '=', 'pilgrims.location_id');
        $pilgrims->join('locations_translations', 'locations.id', '=', 'locations_translations.location_id');
        $pilgrims->join('pilgrims_class', 'pilgrims_class.id', '=', 'pilgrims.pilgrim_class_id');
        $pilgrims->join('pilgrims_class_translations', 'pilgrims_class.id', '=', 'pilgrims_class_translations.pilgrims_class_id');
        $pilgrims->join('buildings_accommodation', 'pilgrims.id', '=', 'buildings_accommodation.pilgrim_id');
        $pilgrims->join('buildings_floors_rooms', 'buildings_floors_rooms.id', '=', 'buildings_accommodation.building_floor_room_id');
        $pilgrims->join('buildings_floors', 'buildings_floors.id', '=', 'buildings_floors_rooms.building_floor_id');
        $pilgrims->join('buildings', 'buildings.id', '=', 'buildings_floors.building_id');
        if (isset($where_array['filter'])) {
            $pilgrims->select(['buildings_accommodation.id', "pilgrims.reservation_no", "pilgrims.gender", "pilgrims.mobile", "pilgrims.image", "pilgrims.name",
                "pilgrims_class_translations.title as pilgrim_class_title", "pilgrims.ssn", "pilgrims.name", "pilgrims.code", "pilgrims.mobile", "buildings_accommodation.id as buildings_accommodation_id",
                "buildings.number as building_number", "buildings_floors.number as floor_number","buildings_floors_rooms.number as room_number", "locations_translations.title as location_title"]);
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
            $pilgrims->orderBy('buildings_floors_rooms.number', 'ASC');
            $pilgrims->orderBy('buildings_floors.number', 'ASC');
            $pilgrims->orderBy('buildings.number', 'ASC');
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
                $pilgrims->whereIn("buildings_accommodation.id", $where_array['destroy']['ids']);
            }
        }
        return $pilgrims;
    }

}
