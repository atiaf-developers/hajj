<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class BusAccommodation extends MyModel {

    protected $table = 'buses_accommodation';

    public static function gePilgrimsWithAccommodation($where_array = array(), $id = false) {
        $pilgrims = Pilgrim::join('locations', 'locations.id', '=', 'pilgrims.location_id');
        $pilgrims->join('locations_translations', 'locations.id', '=', 'locations_translations.location_id');
        $pilgrims->join('buses_accommodation', 'pilgrims.id', '=', 'buses_accommodation.pilgrim_id');
        $pilgrims->join('pilgrims_buses', 'pilgrims_buses.id', '=', 'buses_accommodation.pilgrim_bus_id');
        if(isset($where_array['filter'])){
            $pilgrims->select(['buses_accommodation.id', "pilgrims.reservation_no", "pilgrims.ssn", "pilgrims.name", "pilgrims.code", "buses_accommodation.seat_number",
                "locations_translations.title as location_title","pilgrims_buses.bus_number"]);
        }
       
        $pilgrims->where('locations_translations.locale', static::getLangCode());


        if ($id) {
            $pilgrims->where('pilgrims.id', $id);
            $pilgrims = $pilgrims->first();
            if ($pilgrims) {
                $pilgrims = static::transform($pilgrims);
            }
        } else {
            $pilgrims->orderBy('pilgrims_buses.bus_number', 'ASC');
            $pilgrims->orderBy('buses_accommodation.seat_number', 'ASC');
            $pilgrims = static::handleWhere($pilgrims, $where_array);
            $limit = isset($where_array['filter']['per_page']) ? $where_array['filter']['per_page'] : static::$limit;
            if(isset($where_array['filter'])){
                $pilgrims=$pilgrims->paginate($limit)->appends($where_array['filter']);
            }else{
                $pilgrims= $pilgrims->get();
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
            if (isset($where_array['filter']['code'])) {
                $pilgrims->where("pilgrims.code", $where_array['filter']['code']);
            }
            if (isset($where_array['filter']['reservation_no'])) {
                $pilgrims->where("pilgrims.reservation_no", $where_array['filter']['reservation_no']);
            }
            if (isset($where_array['filter']['bus'])) {
                $pilgrims->where("pilgrims_buses.id", $where_array['filter']['bus']);
            }
            
            //destroy
           
        }else if (isset($where_array['destroy'])) {
             if (isset($where_array['destroy']['ids'])) {
                $pilgrims->whereIn("buses_accommodation.id", $where_array['destroy']['ids']);
                
            }
        }
        return $pilgrims;
    }

}
