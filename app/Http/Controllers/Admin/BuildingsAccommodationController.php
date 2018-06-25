<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BackendController;
use App\Models\Location;
use App\Models\Pilgrim;
use App\Models\PilgrimAccommodation;
use App\Models\PilgrimClass;
use App\Models\Suite;
use App\Models\Building;
use App\Models\BuildingFloor;
use App\Models\BuildingFloorRoom;
use App\Models\BuildingAccommodation;
use App\Models\Lounge;
use App\Helpers\Fcm;
use DB;

class BuildingsAccommodationController extends BackendController {

    private $step_one_rules = array(
//        'location' => 'required',
//        'pilgrim_class' => 'required',
//        'gender' => 'required',
    );
    private $step_two_rules = array(
    );
    private $step_three_rules = array(
    );

    public function index(Request $request) {

        if ($request->all()) {
            foreach ($request->all() as $key => $value) {
                if ($value) {
                    $this->data[$key] = $value;
                }
            }
            //dd($request->all() );
        }
        $this->data['locations'] = Location::getAll();
        $this->data['pilgrims_class'] = PilgrimClass::getAll();
        $this->data['types'] = BuildingAccommodation::$types;
        $this->data['pilgrims'] = BuildingAccommodation::gePilgrimsWithAccommodation(['filter' => $request->all()]);
        return $this->_view('buildings_accommodation/index', 'backend');
    }

    public function create(Request $request) {
        $this->data['locations'] = Location::getAll();
        $this->data['pilgrims_class'] = PilgrimClass::getAll();
        $this->data['types'] = BuildingAccommodation::$types;
        $this->data['buildings'] = Building::orderBy('this_order', 'ASC')->get();
        return $this->_view('buildings_accommodation/create', 'backend');
    }

    public function store(Request $request) {
        //dd($request->all());
        $step = $request->input('step');
        //dd($step);
        if ($step == 1) {
            $rules = $this->step_one_rules;
        } else if ($step == 2) {
            $rules = $this->step_two_rules;
        } else {
            return _json('error', _lang('app.error_is_occured'));
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->errors = $validator->errors()->toArray();
            return _json('error', $this->errors);
        }
        if ($step == 1) {
            $pilgrims = $this->getPilgrimsWithNoAccommodation([
                'location' => $request->input('location'),
                'pilgrim_class' => $request->input('pilgrim_class'),
                'gender' => $request->input('gender')]);
            return _json('success', ['step' => $step, 'pilgrims_count' => $pilgrims->count()]);
        } else if ($step == 2) {
            DB::beginTransaction();
            try {
                if (!$request->input('rooms')) {
                    return _json('error', _lang('app.no_lounges_selected'));
                }
                $rooms = $this->getSelectedRooms($request->input('rooms'));
                //dd($rooms);
                $pilgrims = $this->getPilgrimsWithNoAccommodation([
                    'location' => $request->input('location'),
                    'pilgrim_class' => $request->input('pilgrim_class'),
                    'gender' => $request->input('gender')]);
                //dd($pilgrims);
                if ($pilgrims->count() == 0) {
                    return _json('error', _lang('app.no_pilgrims_for_accommodation'));
                }
                $this->handleAccommodation($pilgrims, $rooms, $request->input('type'));
                DB::commit();
                //dd($request->all());
                return _json('success', ['step' => $step, 'created_at' => date('Y-m-d H-i-s'), 'message' => _lang('app.accommodation_done_successfully')]);
            } catch (\Exception $ex) {
                DB::rollback();
                $message = _lang('app.error_is_occured');
                return _json('error', $ex->getMessage());
            }
        }
    }

    public function notify(Request $request) {
        //dd($request->all());
        try {
            $Fcm = new Fcm;

            $token_and = Pilgrim::join('buildings_accommodation', 'pilgrims.id', '=', 'buildings_accommodation.pilgrim_id')
                    ->where('buildings_accommodation.created_at', $request->input('created_at'))
                    ->where('pilgrims.device_type', 1)
                    ->pluck('pilgrims.device_token')
                    ->toArray();
            $token_ios = Pilgrim::join('buildings_accommodation', 'pilgrims.id', '=', 'buildings_accommodation.pilgrim_id')
                    ->where('buildings_accommodation.created_at', $request->input('created_at'))
                    ->where('pilgrims.device_type', 2)
                    ->pluck('pilgrims.device_token')
                    ->toArray();

            $notification = ['title' => 'HAJJ', 'body' => implode("\n", Pilgrim::$accommodation_phrases), 'type' => 2];
            //$this->create_noti($request->input('request_id'), $notifier_id, $request->input('status'), $notifible_type);
            if (count($token_and) > 0) {
                $Fcm->send($token_and, $notification, 'and');
                //dd($send);
            }
            if (count($token_ios) > 0) {
                $Fcm->send($token_ios, $notification, 'ios');
            }
            return _json('success', _lang('app.notification_sent_successfully'));
        } catch (\Exception $ex) {
            $message = _lang('app.error_is_occured');
            return _json('error', $message);
        }
    }

    public function destroy(Request $request) {

        $id = $request->input('id');
        DB::beginTransaction();
        try {
            $BuildingFloorRoom = BuildingFloorRoom::join('buildings_accommodation', 'buildings_floors_rooms.id', '=', 'buildings_accommodation.building_floor_room_id')
                    ->where('buildings_accommodation.id', $id)
                    ->select('buildings_floors_rooms.id', 'buildings_floors_rooms.remaining_available_of_accommodation')
                    ->first();
            if ($BuildingFloorRoom) {

                $BuildingFloorRoom->remaining_available_of_accommodation = $BuildingFloorRoom->remaining_available_of_accommodation - 1;
                $BuildingFloorRoom->save();
                BuildingAccommodation::where('id', $id)->delete();
            }

            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            dd($ex);
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'));
        }
    }

    public function getLounges($suite_id) {
        $suites = Lounge::where('suite_id', $suite_id)
                ->whereRaw('(available_of_accommodation-remaining_available_of_accommodation) > 0')
                ->select('id', 'number', DB::RAW('(available_of_accommodation-remaining_available_of_accommodation) as available'))
                ->get()
                ->toArray();
        return _json('success', $suites);
    }

    private function handleAccommodation($pilgrims, $rooms, $type) {

        //dd($pilgrims);
        $filename = time() . mt_rand(1, 1000000) . '.csv';
        $path = base_path($filename);
        $file = fopen($path, 'w');
        fputcsv($file, array('building_floor_room_id', 'pilgrim_id', 'type'));
        $arr = array();
        $count_pilgrims_for_accommodation = 0;
        if ($pilgrims->count() > 0) {
            foreach ($pilgrims as $one) {
                $arr[$one->reservation_no][$one->id] = $one->id;
            }
            //dd($arr);
            if (count($rooms) > 0) {
                foreach ($rooms as $key => $loungue) {
                    foreach ($arr as $reservation_no => $one) {
                        if (($rooms[$key]['available'] - $rooms[$key]['pilgrims']) == 0) {
                            break;
                        }
                        foreach ($one as $id) {
                            //dd($id);
                            //if (count($id) > 0) {
                            if (($rooms[$key]['available'] - $rooms[$key]['pilgrims']) == 0) {
                                break;
                            }
                            $rooms[$key]['pilgrims'] += 1;
                            $count_pilgrims_for_accommodation++;
                            fputcsv($file, array('building_floor_room_id' => $key, 'pilgrim_id' => $id, 'type' => $type), ";");
                            unset($arr[$reservation_no][$id]);
                            //}
                        }
                        //dd($rooms);
                    }
                }
                //dd($rooms);
                $rooms_update = [];
                if ($count_pilgrims_for_accommodation > 0) {
                    $this->_import_csv_pilgrims_rooms($path);

                    foreach ($rooms as $lounge_id => $lounge_info) {
                        $rooms_update['remaining_available_of_accommodation'][] = [
                            'id' => $lounge_id,
                            'value' => $lounge_info['pilgrims'] + $lounge_info['remaining_available_of_accommodation']
                        ];
                    }
                    $this->updateValues('App\Models\BuildingFloorRoom', $rooms_update);
                }
            }
            fclose($file);
            unlink($path);
        }
    }

    private function getPilgrimsWithNoAccommodation($where_array = array()) {
        $pilgrims = Pilgrim::leftJoin('buildings_accommodation', 'pilgrims.id', '=', 'buildings_accommodation.pilgrim_id')
                ->leftJoin('tents_accommodation', 'pilgrims.id', '=', 'tents_accommodation.pilgrim_id')
                ->leftJoin('suites_accommodation', 'pilgrims.id', '=', 'suites_accommodation.pilgrim_id')
                ->where('pilgrims.location_id', $where_array['location'])
                ->where('pilgrims.pilgrim_class_id', $where_array['pilgrim_class'])
                ->where('pilgrims.gender', $where_array['gender'])
                ->whereNull('buildings_accommodation.id')
                ->whereNull('tents_accommodation.id')
                ->whereNull('suites_accommodation.id')
                ->select('pilgrims.id', 'pilgrims.reservation_no')
                ->get();
        return $pilgrims;
    }

    private function getSelectedRooms($rooms) {
        //dd($lounges);
        //$lounges=[1,2];
        $lounges = BuildingFloorRoom::whereIn('id', $rooms)
                ->whereRaw('(available_of_accommodation-remaining_available_of_accommodation) > 0')
                ->select('id', 'remaining_available_of_accommodation', DB::RAW('(available_of_accommodation-remaining_available_of_accommodation) as available'), DB::RAW('0 as pilgrims'))
                ->orderBy('number', 'asc')
                ->get()
                ->keyBy('id')
                ->toArray();
        return $lounges;
    }

    public function getRooms($floor_id) {
        $rooms = BuildingFloorRoom::where('building_floor_id', $floor_id)
                ->whereRaw('(available_of_accommodation-remaining_available_of_accommodation) > 0')
                ->select('id', 'number', DB::RAW('(available_of_accommodation-remaining_available_of_accommodation) as available'))
                ->get()
                ->toArray();
        return _json('success', $rooms);
    }

    public function getFloors(Request $request) {
        //dd($lounges);
        $floors = BuildingFloor::where('building_id', $request->input('building'))
                ->where('gender', $request->input('gender'))
                ->select('id', 'number')
                ->get()
                ->toArray();
        return _json('success', $floors);
    }

    private function _import_csv_pilgrims_rooms($csv, $terminated = ";") {

        $query = sprintf("LOAD DATA local INFILE '%s' INTO TABLE buildings_accommodation CHARACTER SET utf8 FIELDS TERMINATED BY '$terminated' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\\n' IGNORE 1 LINES (`building_floor_room_id`, `pilgrim_id`,`type` ) SET type=TRIM(BOTH '\\r' FROM type),updated_at=current_timestamp(),created_at=current_timestamp()", addslashes($csv));
        return DB::connection()->getpdo()->exec($query);
    }

}
