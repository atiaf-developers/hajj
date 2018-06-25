<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BackendController;
use App\Models\Location;
use App\Models\Pilgrim;
use App\Models\Suite;
use App\Models\Building;
use App\Models\BuildingFloor;
use App\Models\BuildingFloorRoom;
use App\Models\SuiteAccommodation;
use App\Models\BuildingAccommodation;
use App\Models\TentAccommodation;
use App\Models\BusAccommodation;
use App\Models\Tent;
use App\Models\Lounge;
use App\Models\PilgrimsBus;
use DB;

class ManualAccommodationController extends BackendController {

    private $rules = array(
        'pilgrim_code' => 'required',
    );

    public function index() {

        $this->data['suites_accommodation_types'] = SuiteAccommodation::$types;
        $this->data['buildings_accommodation_types'] = BuildingAccommodation::$types;
        //dd(  $this->data['buildings_accommodation_types'] );
        return $this->_view('manual_accommodation/index', 'backend');
    }

    public function store(Request $request) {
        //dd($request->all());
        $type_of_accommodation = $request->input('type_of_accommodation');
        //dd($step);
        if ($type_of_accommodation == 1) {
            $this->rules['suite'] = 'required';
            $this->rules['lounge'] = 'required';
        } else if ($type_of_accommodation == 2) {
            $this->rules['building'] = 'required';
            $this->rules['floor'] = 'required';
            $this->rules['room'] = 'required';
        } else if ($type_of_accommodation == 3) {
            $this->rules['tent'] = 'required';
        } else if ($type_of_accommodation == 4) {
            $this->rules['bus'] = 'required';
        } else {
            return _json('error', _lang('app.error_is_occured'), 400);
        }
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $this->errors = $validator->errors()->toArray();
            return _json('error', $this->errors);
        }
        DB::beginTransaction();
        try {
            if (in_array($type_of_accommodation, [1, 2, 3])) {
                $pilgrim = $this->getPilgrimWithNoAccommodation(['pilgrim_code' => $request->input('pilgrim_code')]);
            } else if ($type_of_accommodation == 4) {
                $pilgrim = $this->getPilgrimsWithNoBusesAccommodation(['pilgrim_code' => $request->input('pilgrim_code')]);
            }
            if (!$pilgrim) {
                return _json('error', _lang('app.this_pilgrim_has_been_accommodation'), 400);
            }


            if ($type_of_accommodation == 1) {
                $lounge_id = $request->input('lounge');
                $lounges = $this->getSelectedLounges([$lounge_id]);
                if (count($lounges) > 0) {

                    Lounge::where('id', $request->input('lounge'))->update(['remaining_available_of_accommodation' => $lounges[$lounge_id]['remaining_available_of_accommodation'] + 1]);
                    $SuiteAccommodation = new SuiteAccommodation;
                    $SuiteAccommodation->pilgrim_id = $pilgrim->id;
                    $SuiteAccommodation->lounge_id = $lounge_id;
                    $SuiteAccommodation->number = $lounges[$lounge_id]['number'] + 1;
                    $SuiteAccommodation->type = $request->input('suites_accommodation_type');
                    $SuiteAccommodation->save();
                }
            } else if ($type_of_accommodation == 2) {
                $room_id = $request->input('room');
                $rooms = $this->getSelectedRooms([$room_id]);
                if (count($rooms) > 0) {
                    BuildingFloorRoom::where('id', $request->input('room'))->update(['remaining_available_of_accommodation' => $rooms[$room_id]['remaining_available_of_accommodation'] + 1]);
                    $BuildingAccommodation = new BuildingAccommodation;
                    $BuildingAccommodation->pilgrim_id = $pilgrim->id;
                    $BuildingAccommodation->building_floor_room_id = $room_id;
                    $BuildingAccommodation->type = $request->input('buildings_accommodation_type');
                    $BuildingAccommodation->save();
                }
            } else if ($type_of_accommodation == 3) {
                $tents = $this->getSelectedTents([$request->input('tent')]);
                if (count($tents) > 0) {
                    $tent_id = $request->input('tent');
                    BuildingFloorRoom::where('id', $request->input('tent'))->update(['remaining_available_of_accommodation' => $tents[$tent_id]['remaining_available_of_accommodation'] + 1]);
                    $TentAccommodation = new TentAccommodation;
                    $TentAccommodation->pilgrim_id = $pilgrim->id;
                    $TentAccommodation->tent_id = $tent_id;
                    $TentAccommodation->save();
                }
            } else if ($type_of_accommodation == 4) {
                $bus_id = $request->input('bus');
                //dd($bus_id);
                $buses = $this->getSelectedBuses([$bus_id]);
                if (count($buses) > 0) {

                    PilgrimsBus::where('id', $bus_id)->update(['remaining_num_of_seats' => $buses[$bus_id]['remaining_num_of_seats'] + 1]);
                    $BusAccommodation = new BusAccommodation;
                    $BusAccommodation->pilgrim_id = $pilgrim->id;
                    $BusAccommodation->pilgrim_bus_id = $bus_id;
                    $BusAccommodation->seat_number = $buses[$bus_id]['seat_number'] + 1;
                    $BusAccommodation->save();
                }
            }
            DB::commit();
            return _json('success', _lang('app.accommodation_done_successfully'));
        } catch (\Exception $ex) {
            dd($ex);
            DB::rollback();
            $message = _lang('app.error_is_occured');
            return _json('error', $ex->getMessage() . $ex->getLine());
        }
    }

    private function getSelectedTents($tents) {
        //dd($lounges);
        //$lounges=[1,2];
        $tents_arr = Tent::whereIn('id', $tents)
                ->whereRaw('(available_of_accommodation-remaining_available_of_accommodation) > 0')
                ->select('id', 'remaining_available_of_accommodation', DB::RAW('(available_of_accommodation-remaining_available_of_accommodation) as available'), DB::RAW('0 as pilgrims'))
                ->orderBy('number', 'asc')
                ->get()
                ->keyBy('id')
                ->toArray();
        return $tents_arr;
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

    private function getSelectedLounges($lounges) {
        //dd($lounges);
        //$lounges=[1,2];
        $lounges = Lounge::leftJoin(
                        DB::raw("
                            (select
                            `suites_accommodation`.*
                            from `suites_accommodation`
                            order by suites_accommodation.number DESC
                            limit 1
                            ) `suites_accommodation`
                            "), 'lounges.id', '=', 'suites_accommodation.lounge_id'
                )
                ->whereIn('lounges.id', $lounges)
                ->whereRaw('(available_of_accommodation-remaining_available_of_accommodation) > 0')
                ->select('lounges.id', 'lounges.remaining_available_of_accommodation', DB::RAW('(lounges.available_of_accommodation-lounges.remaining_available_of_accommodation) as available'), DB::RAW('CASE WHEN suites_accommodation.number IS NULL THEN 0 ELSE suites_accommodation.number END as number'), DB::RAW('0 as pilgrims'))
                ->orderBy('suites_accommodation.number', 'asc')
                ->groupBy('lounges.id')
                ->get()
                ->keyBy('id')
                ->toArray();
        return $lounges;
    }

    public function getDataForAccommodation(Request $request) {
        try {
            $type_of_accommodation = $request->input('type_of_accommodation');
            if (in_array($type_of_accommodation, [1, 2, 3])) {
                $pilgrim = $this->getPilgrimWithNoAccommodation(['pilgrim_code' => $request->input('pilgrim_code')]);
            } else if ($type_of_accommodation == 4) {
                $pilgrim = $this->getPilgrimsWithNoBusesAccommodation(['pilgrim_code' => $request->input('pilgrim_code')]);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
            if (!$pilgrim) {
                return _json('error', _lang('app.pilgrim_is_not_found'), 400);
            }

            if ($type_of_accommodation == 1) {
                $suites = $this->getSuites(['gender' => $pilgrim->gender]);
                return _json('success', $suites);
            } else if ($type_of_accommodation == 2) {
                $buildings = Building::orderBy('this_order', 'ASC')->get()->toArray();
                return _json('success', $buildings);
            } else if ($type_of_accommodation == 3) {
                $tents = Tent::whereRaw('(available_of_accommodation-remaining_available_of_accommodation) > 0')
                        ->where('gender', $pilgrim->gender)
                        ->select('id', 'number', DB::RAW('(available_of_accommodation-remaining_available_of_accommodation) as available'))
                        ->get()
                        ->toArray();
                //dd($tents);
                return _json('success', $tents);
            } else if ($type_of_accommodation == 4) {
                //dd($this->getBuses());
                return _json('success', $this->getBuses());
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        } catch (\Exception $ex) {
            $message = _lang('app.error_is_occured');
            return _json('error', $ex->getMessage() . $ex->getLine());
        }
    }

    private function getPilgrimWithNoAccommodation($where_array = array()) {
        $pilgrims = Pilgrim::leftJoin('suites_accommodation', 'pilgrims.id', '=', 'suites_accommodation.pilgrim_id')
                ->leftJoin('tents_accommodation', 'pilgrims.id', '=', 'tents_accommodation.pilgrim_id')
                ->leftJoin('buildings_accommodation', 'pilgrims.id', '=', 'buildings_accommodation.pilgrim_id')
                ->where('pilgrims.code', $where_array['pilgrim_code'])
                ->whereNull('buildings_accommodation.id')
                ->whereNull('tents_accommodation.id')
                ->whereNull('suites_accommodation.id')
                ->select('pilgrims.id', 'pilgrims.reservation_no', 'pilgrims.gender')
                ->first();
        return $pilgrims;
    }

    private function getPilgrimsWithNoBusesAccommodation($where_array = array()) {
        $pilgrims = Pilgrim::leftJoin('buses_accommodation', 'pilgrims.id', '=', 'buses_accommodation.pilgrim_id')
                ->where('pilgrims.code', $where_array['pilgrim_code'])
                ->whereNull('buses_accommodation.id')
                ->select('pilgrims.id', 'pilgrims.reservation_no', 'pilgrims.gender')
                ->first();
        return $pilgrims;
    }

    private function getSuites($where_array = array()) {
        $suites = Suite::where('gender', $where_array['gender'])->select('id', 'number')->get()->toArray();
        return $suites;
    }

    public function getFloors(Request $request) {
        $pilgrim = Pilgrim::where('code', $request->input('pilgrim_code'))->first();
        $floors = [];
        if ($pilgrim) {
            $floors = BuildingFloor::where('building_id', $request->input('building'))
                    ->where('gender', $pilgrim->gender)
                    ->select('id', 'number')
                    ->get()
                    ->toArray();
        }

        return _json('success', $floors);
    }

    public function getRooms(Request $request) {
        $rooms = BuildingFloorRoom::where('building_floor_id', $request->input('floor'))
                ->whereRaw('(available_of_accommodation-remaining_available_of_accommodation) > 0')
                ->select('id', 'number', DB::RAW('(available_of_accommodation-remaining_available_of_accommodation) as available'))
                ->get()
                ->toArray();
        return _json('success', $rooms);
    }

    public function getLounges(Request $request) {
        $suites = Lounge::where('suite_id', $request->input('suite'))
                ->whereRaw('(available_of_accommodation-remaining_available_of_accommodation) > 0')
                ->select('id', 'number', DB::RAW('(available_of_accommodation-remaining_available_of_accommodation) as available'))
                ->get()
                ->toArray();
        return _json('success', $suites);
    }

    public function getBuses() {
        $buses = PilgrimsBus::whereRaw('(num_of_seats-remaining_num_of_seats) > 0')
                ->select('id', 'bus_number as number', DB::RAW('(num_of_seats-remaining_num_of_seats) as available'))
                ->get()
                ->toArray();
        return $buses;
    }

    private function handleAccommodation($pilgrims, $buses) {

        //dd($pilgrims);
        $filename = time() . mt_rand(1, 1000000) . '.csv';
        $path = base_path($filename);
        $file = fopen($path, 'w');
        fputcsv($file, array('pilgrim_bus_id', 'pilgrim_id', 'seat_number'));
        $arr = array();
        $count_pilgrims_for_accommodation = 0;
        if ($pilgrims->count() > 0) {
            foreach ($pilgrims as $one) {
                $arr[$one->reservation_no][$one->id] = $one->id;
            }
            //dd($arr);
            if (count($buses) > 0) {
                foreach ($buses as $key => $buse) {
                    foreach ($arr as $reservation_no => $one) {
                        if (($buses[$key]['available'] - $buses[$key]['pilgrims']) == 0) {
                            break;
                        }
                        foreach ($one as $id) {
                            //dd($id);
                            //if (count($id) > 0) {
                            if (($buses[$key]['available'] - $buses[$key]['pilgrims']) == 0) {
                                break;
                            }
                            $buses[$key]['pilgrims'] += 1;
                            $buses[$key]['seat_number'] += 1;
                            $count_pilgrims_for_accommodation++;
                            fputcsv($file, array('pilgrim_bus_id' => $key, 'pilgrim_id' => $id, 'seat_number' => $buses[$key]['seat_number']), ";");
                            unset($arr[$reservation_no][$id]);
                            //}
                        }
                        //dd($rooms);
                    }
                }
                //dd($buses);
                $buses_update = [];
                if ($count_pilgrims_for_accommodation > 0) {
                    $this->_import_csv_pilgrims_buses($path);
                    foreach ($buses as $bus_id => $bus_info) {
                        $buses_update['remaining_num_of_seats'][] = [
                            'id' => $bus_id,
                            'value' => $bus_info['pilgrims'] + $bus_info['remaining_num_of_seats']
                        ];
                    }
                    $this->updateValues('App\Models\PilgrimsBus', $buses_update);
                }
            }
            fclose($file);
            unlink($path);
        }
    }

    private function getSelectedBuses($buses_ids) {
        //dd($lounges);
        $buses = PilgrimsBus::leftJoin(
                        DB::raw("
                        (select
                        `buses_accommodation`.*
                         from `buses_accommodation`
                        order by buses_accommodation.seat_number DESC
                        limit 1
                        ) `buses_accommodation`
                        "), 'pilgrims_buses.id', '=', 'buses_accommodation.pilgrim_bus_id'
                )
                ->whereIn('pilgrims_buses.id', $buses_ids)
                ->select('pilgrims_buses.id', 'pilgrims_buses.remaining_num_of_seats', DB::RAW('CASE WHEN buses_accommodation.seat_number IS NULL THEN 0 ELSE buses_accommodation.seat_number END as seat_number'), DB::RAW('(pilgrims_buses.num_of_seats-pilgrims_buses.remaining_num_of_seats) as available'), DB::RAW('0 as pilgrims'))
                ->get()
                ->keyBy('id')
                ->toArray();
        return $buses;
    }

    private function _import_csv_pilgrims_buses($csv, $terminated = ";") {

        $query = sprintf("LOAD DATA local INFILE '%s' INTO TABLE manual_accommodation CHARACTER SET utf8 FIELDS TERMINATED BY '$terminated' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\\n' IGNORE 1 LINES (`pilgrim_bus_id`, `pilgrim_id`,`seat_number` ) SET seat_number=TRIM(BOTH '\\r' FROM seat_number)", addslashes($csv));
        return DB::connection()->getpdo()->exec($query);
    }

}
