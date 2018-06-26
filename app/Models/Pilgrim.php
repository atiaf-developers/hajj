<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pilgrim extends MyModel {

    protected $table = "pilgrims";

    public static function getAll($where_array = array()) {
        $pilgrims = static::join('locations', 'locations.id', '=', 'pilgrims.location_id');
        $pilgrims->join('locations_translations', 'locations.id', '=', 'locations_translations.location_id');
        $pilgrims->join('pilgrims_class', 'pilgrims_class.id', '=', 'pilgrims.pilgrim_class_id');
        $pilgrims->join('pilgrims_class_translations', 'pilgrims_class.id', '=', 'pilgrims_class_translations.pilgrims_class_id');
        $pilgrims->join('supervisors as s2', 's2.id', '=', 'pilgrims_class.supervisor_id');
        $pilgrims->join('supervisors as s3', 's3.id', '=', 'locations.supervisor_id');
        $pilgrims->leftJoin('buses_accommodation', 'pilgrims.id', '=', 'buses_accommodation.pilgrim_id');
        $pilgrims->leftJoin('pilgrims_buses', 'pilgrims_buses.id', '=', 'buses_accommodation.pilgrim_bus_id');
        $pilgrims->leftJoin('supervisors as s1', 's1.id', '=', 'pilgrims_buses.supervisor_id');

        //suites accommodation
        $pilgrims->leftJoin('suites_accommodation', 'pilgrims.id', '=', 'suites_accommodation.pilgrim_id');
        $pilgrims->leftJoin('lounges', 'lounges.id', '=', 'suites_accommodation.lounge_id');
        $pilgrims->leftJoin('suites', 'suites.id', '=', 'lounges.suite_id');

        //buildings accommodation
        $pilgrims->leftJoin('buildings_accommodation', 'pilgrims.id', '=', 'buildings_accommodation.pilgrim_id');
        $pilgrims->leftJoin('buildings_floors_rooms', 'buildings_floors_rooms.id', '=', 'buildings_accommodation.building_floor_room_id');
        $pilgrims->leftJoin('buildings_floors', 'buildings_floors.id', '=', 'buildings_floors_rooms.building_floor_id');
        $pilgrims->leftJoin('buildings', 'buildings.id', '=', 'buildings_floors.building_id');

        //tents accommodation
        $pilgrims->leftJoin('tents_accommodation', 'pilgrims.id', '=', 'tents_accommodation.pilgrim_id');
        $pilgrims->leftJoin('tents', 'tents.id', '=', 'tents_accommodation.tent_id');

        $pilgrims->select(['pilgrims.id', "pilgrims.gender", "pilgrims.nationality", "pilgrims.reservation_no", "pilgrims.gender", "pilgrims.mobile", "pilgrims.image", "pilgrims.qr_image", "pilgrims_class_translations.title as pilgrim_class_title", "pilgrims.ssn", "pilgrims.name", "pilgrims.code",
            "pilgrims.mobile", "pilgrims_buses.bus_number", "s1.name as bus_supervisor_name", "s1.supervisor_image as bus_supervisor_image", "pilgrims.active",
            "s1.contact_numbers as bus_supervisor_contact_numbers", "suites_accommodation.id as suites_accommodation_id",
            "suites.number as suite_number", "lounges.number as lounge_number", "suites_accommodation.number as lounge_seat_number",
            "suites_accommodation.type as suites_accommodation_type", "s2.name as class_supervisor_name", "s2.supervisor_image as class_supervisor_image",
            "s2.contact_numbers as class_supervisor_contact_numbers", "s3.name as location_supervisor_name", "s3.supervisor_image as location_supervisor_image",
            "buildings_accommodation.id as buildings_accommodation_id", "buildings_accommodation.type as buildings_accommodation_type", "buildings.number as building_number", "buildings_floors.number as floor_number", "buildings_floors_rooms.number as room_number",
            "tents_accommodation.id as tents_accommodation_id", "tents.number as tent_number", "tents.type as tent_type",
            "s3.contact_numbers as location_supervisor_contact_numbers", "locations_translations.title as location_title"]);
        $pilgrims->where('pilgrims_class_translations.locale', static::getLangCode());
        $pilgrims->where('locations_translations.locale', static::getLangCode());


        if (isset($where_array['pilgrims.id'])) {
            $pilgrims->where(function ($query) use($where_array) {
                $query->where('pilgrims.id', $where_array['pilgrims.id']);
                $query->orWhere('pilgrims.code', $where_array['pilgrims.id']);
            });
            $pilgrims = $pilgrims->first();
            if ($pilgrims) {
                $pilgrims = static::transform($pilgrims);
            }
        } else {
            $pilgrims->orderBy('pilgrims.created_at', 'DESC');
            if (!empty($where_array)) {
                foreach ($where_array as $key => $value) {
                    if ($key == 'search') {
                        $pilgrims->whereRaw(static::handleKeywordWhere(['pilgrims.name', 'pilgrims.reservation_no', 'pilgrims.ssn'], $value));
                    } else if ($key == 'locations.id') {
                        $pilgrims->whereIn($key, $value);
                    } else {
                        $pilgrims->where($key, $value);
                    }
                }
            }
            $pilgrims = $pilgrims->paginate(static::$limit);
            $pilgrims = $pilgrims->getCollection()->transform(function($pilgrim, $key) {
                return static::transform($pilgrim);
            });
        }

        return $pilgrims;
    }

    public static function getAllAdmin($where_array = array()) {
        $pilgrims = static::join('locations', 'locations.id', '=', 'pilgrims.location_id');
        $pilgrims->join('locations_translations', 'locations.id', '=', 'locations_translations.location_id');
        $pilgrims->join('pilgrims_class', 'pilgrims_class.id', '=', 'pilgrims.pilgrim_class_id');
        $pilgrims->join('pilgrims_class_translations', 'pilgrims_class.id', '=', 'pilgrims_class_translations.pilgrims_class_id');
        $pilgrims->join('supervisors as s2', 's2.id', '=', 'pilgrims_class.supervisor_id');
        $pilgrims->join('supervisors as s3', 's3.id', '=', 'locations.supervisor_id');
        $pilgrims->leftJoin('buses_accommodation', 'pilgrims.id', '=', 'buses_accommodation.pilgrim_id');
        $pilgrims->leftJoin('pilgrims_buses', 'pilgrims_buses.id', '=', 'buses_accommodation.pilgrim_bus_id');
        $pilgrims->leftJoin('supervisors as s1', 's1.id', '=', 'pilgrims_buses.supervisor_id');

        //suites accommodation
        $pilgrims->leftJoin('suites_accommodation', 'pilgrims.id', '=', 'suites_accommodation.pilgrim_id');
        $pilgrims->leftJoin('lounges', 'lounges.id', '=', 'suites_accommodation.lounge_id');
        $pilgrims->leftJoin('suites', 'suites.id', '=', 'lounges.suite_id');

        //buildings accommodation
        $pilgrims->leftJoin('buildings_accommodation', 'pilgrims.id', '=', 'buildings_accommodation.pilgrim_id');
        $pilgrims->leftJoin('buildings_floors_rooms', 'buildings_floors_rooms.id', '=', 'buildings_accommodation.building_floor_room_id');
        $pilgrims->leftJoin('buildings_floors', 'buildings_floors.id', '=', 'buildings_floors_rooms.building_floor_id');
        $pilgrims->leftJoin('buildings', 'buildings.id', '=', 'buildings_floors.building_id');

        //tents accommodation
        $pilgrims->leftJoin('tents_accommodation', 'pilgrims.id', '=', 'tents_accommodation.pilgrim_id');
        $pilgrims->leftJoin('tents', 'tents.id', '=', 'tents_accommodation.tent_id');

        $pilgrims->select(['pilgrims.id', "pilgrims.gender", "pilgrims.nationality", "pilgrims.reservation_no", "pilgrims.gender", "pilgrims.mobile", "pilgrims.image", "pilgrims.qr_image", "pilgrims_class_translations.title as pilgrim_class_title", "pilgrims.ssn", "pilgrims.name", "pilgrims.code",
            "pilgrims.mobile", "pilgrims_buses.bus_number", "s1.name as bus_supervisor_name", "s1.supervisor_image as bus_supervisor_image", "pilgrims.active",
            "s1.contact_numbers as bus_supervisor_contact_numbers", "suites_accommodation.id as suites_accommodation_id",
            "suites.number as suite_number", "lounges.number as lounge_number", "suites_accommodation.number as lounge_seat_number",
            "suites_accommodation.type as suites_accommodation_type", "s2.name as class_supervisor_name", "s2.supervisor_image as class_supervisor_image",
            "s2.contact_numbers as class_supervisor_contact_numbers", "s3.name as location_supervisor_name", "s3.supervisor_image as location_supervisor_image",
            "buildings_accommodation.id as buildings_accommodation_id", "buildings_accommodation.type as buildings_accommodation_type", "buildings.number as building_number", "buildings_floors.number as floor_number", "buildings_floors_rooms.number as room_number",
            "tents_accommodation.id as tents_accommodation_id", "tents.number as tent_number", "tents.type as tent_type",
            "s3.contact_numbers as location_supervisor_contact_numbers", "locations_translations.title as location_title"]);
        $pilgrims->where('pilgrims_class_translations.locale', static::getLangCode());
        $pilgrims->where('locations_translations.locale', static::getLangCode());


        if (isset($where_array['pilgrims.id'])) {
            $pilgrims->where(function ($query) use($where_array) {
                $query->where('pilgrims.id', $where_array['pilgrims.id']);
                $query->orWhere('pilgrims.code', $where_array['pilgrims.id']);
            });
        }
        $pilgrims = $pilgrims->first();
        if ($pilgrims) {
            $pilgrims = static::transformAdmin($pilgrims);
        }

        return $pilgrims;
    }

    public static function transform($item) {
        $transformer = new \stdClass();

        $transformer->id = $item->id;
        $transformer->pilgrim_class_title = $item->pilgrim_class_title;
        $transformer->ssn = $item->ssn;
        $transformer->gender = $item->gender;
        $transformer->name = $item->name;
        $transformer->code = $item->code;
        $transformer->active = $item->active;
        $transformer->nationality = $item->nationality;
        if (!$item->image && $item->gender == 1) {
            $item->image = 'male.png';
        } else if (!$item->image && $item->gender == 2) {
            $item->image = 'female.png';
        }

        $transformer->reservation_no = $item->reservation_no;
        $transformer->location_title = $item->location_title;
        $transformer->image = url('public/uploads/pilgrims/' . $item->image);
        $transformer->mobile = $item->mobile;
        $transformer->qr_image = url('public/uploads/pilgrims/' . $item->qr_image);
        $transformer->bus_number = $item->bus_number;
        $transformer->bus_supervisor_name = $item->bus_supervisor_name;
        $transformer->bus_supervisor_image = url('public/uploads/supervisors/' . $item->bus_supervisor_image);
        $transformer->bus_supervisor_contact_numbers = explode(",", $item->bus_supervisor_contact_numbers);
        $transformer->class_supervisor_name = $item->class_supervisor_name;
        $transformer->class_supervisor_image = url('public/uploads/supervisors/' . $item->class_supervisor_image);
        $transformer->class_supervisor_contact_numbers = explode(",", $item->class_supervisor_contact_numbers);
        $transformer->location_supervisor_name = $item->location_supervisor_name;
        $transformer->location_supervisor_image = url('public/uploads/supervisors/' . $item->location_supervisor_image);
        $transformer->location_supervisor_contact_numbers = explode(",", $item->location_supervisor_contact_numbers);
        $transformer->accommodation = [];
        if ($item->suites_accommodation_id) {
            $transformer->accommodation[0] = [
                'name' => _lang('app.suite'),
                'value' => $item->suite_number
            ];
            $transformer->accommodation[1] = [
                'name' => _lang('app.lounge'),
                'value' => $item->lounge_number
            ];
            if ($item->suites_accommodation_type == 0) {
                $transformer->accommodation[2] = [
                    'name' => _lang('app.seat'),
                    'value' => $item->lounge_seat_number
                ];
            } else if ($item->suites_accommodation_type == 1) {
                $transformer->accommodation[2] = [
                    'name' => _lang('app.chair'),
                    'value' => $item->lounge_seat_number
                ];
            } else if ($item->suites_accommodation_type == 2) {
                $transformer->accommodation[2] = [
                    'name' => _lang('app.bed'),
                    'value' => $item->lounge_seat_number
                ];
            }
        }
        if ($item->buildings_accommodation_id) {

            if ($item->buildings_accommodation_type == 0 || $item->buildings_accommodation_type == 1) {
                $transformer->accommodation[0] = [
                    'name' => _lang('app.building'),
                    'value' => $item->building_number
                ];
                $transformer->accommodation[1] = [
                    'name' => _lang('app.floor'),
                    'value' => $item->floor_number
                ];
                $transformer->accommodation[2] = [
                    'name' => _lang('app.room'),
                    'value' => $item->room_number
                ];
            } else if ($item->buildings_accommodation_type == 2) {
                $transformer->accommodation[0] = [
                    'name' => _lang('app.floor'),
                    'value' => $item->floor_number
                ];
                $transformer->accommodation[1] = [
                    'name' => _lang('app.room'),
                    'value' => $item->room_number
                ];
            }
        }
        if ($item->tents_accommodation_id) {

            $transformer->accommodation[0] = [
                'name' => $item->tent_type == 1 ? _lang('app.tent') : _lang('app.lounge'),
                'value' => $item->tent_number
            ];
        }


        return $transformer;
    }

    public static function transformAdmin($item) {
        $transformer = new \stdClass();

        $transformer->id = $item->id;
        $transformer->pilgrim_class_title = $item->pilgrim_class_title;
        $transformer->ssn = $item->ssn;
        $transformer->gender = $item->gender;
        $transformer->name = $item->name;
        $transformer->code = $item->code;
        $transformer->active = $item->active;
        $transformer->nationality = $item->nationality;
        if (!$item->image && $item->gender == 1) {
            $item->image = 'male.png';
        } else if (!$item->image && $item->gender == 2) {
            $item->image = 'female.png';
        }

        $transformer->reservation_no = $item->reservation_no;
        $transformer->location_title = $item->location_title;
        $transformer->image = url('public/uploads/pilgrims/' . $item->image);
        $transformer->mobile = $item->mobile;
        $transformer->qr_image = url('public/uploads/pilgrims/' . $item->qr_image);
        $transformer->bus_number = $item->bus_number;
        $transformer->bus_supervisor_name = $item->bus_supervisor_name;
        $transformer->bus_supervisor_image = url('public/uploads/supervisors/' . $item->bus_supervisor_image);
        $transformer->bus_supervisor_contact_numbers = explode(",", $item->bus_supervisor_contact_numbers);
        $transformer->class_supervisor_name = $item->class_supervisor_name;
        $transformer->class_supervisor_image = url('public/uploads/supervisors/' . $item->class_supervisor_image);
        $transformer->class_supervisor_contact_numbers = explode(",", $item->class_supervisor_contact_numbers);
        $transformer->location_supervisor_name = $item->location_supervisor_name;
        $transformer->location_supervisor_image = url('public/uploads/supervisors/' . $item->location_supervisor_image);
        $transformer->location_supervisor_contact_numbers = explode(",", $item->location_supervisor_contact_numbers);
        $transformer->accommodation = [];
            if ($item->suites_accommodation_id) {
            $transformer->accommodation[0] = [
                'name' => 'suite',
                'value' => $item->suite_number
            ];
            $transformer->accommodation[1] = [
                'name' => 'lounge',
                'value' => $item->lounge_number
            ];
            if ($item->suites_accommodation_type == 0) {
                $transformer->accommodation[2] = [
                    'name' => 'seat',
                    'value' => $item->lounge_seat_number
                ];
            } else if ($item->suites_accommodation_type == 1) {
                $transformer->accommodation[2] = [
                    'name' => 'chair',
                    'value' => $item->lounge_seat_number
                ];
            } else if ($item->suites_accommodation_type == 2) {
                $transformer->accommodation[2] = [
                    'name' => 'bed',
                    'value' => $item->lounge_seat_number
                ];
            }
        }
        if ($item->buildings_accommodation_id) {

            if ($item->buildings_accommodation_type == 0 || $item->buildings_accommodation_type == 1) {
                $transformer->accommodation[0] = [
                    'name' => 'building',
                    'value' => $item->building_number
                ];
                $transformer->accommodation[1] = [
                    'name' => 'floor',
                    'value' => $item->floor_number
                ];
                $transformer->accommodation[2] = [
                    'name' => 'room',
                    'value' => $item->room_number
                ];
            } else if ($item->buildings_accommodation_type == 2) {
                $transformer->accommodation[0] = [
                    'name' => 'floor',
                    'value' => $item->floor_number
                ];
                $transformer->accommodation[1] = [
                    'name' => 'room',
                    'value' => $item->room_number
                ];
            }
        }
        if ($item->tents_accommodation_id) {

            $transformer->accommodation[0] = [
                'name' => $item->tent_type == 1 ? 'tent' : 'lounge',
                'value' => $item->tent_number
            ];
        }


        return $transformer;
    }

}
