<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BackendController;
use App\Models\Location;
use App\Models\Pilgrim;
use App\Models\PilgrimsBus;
use App\Models\BusAccommodation;
use App\Models\Lounge;
use App\Helpers\Fcm;
use DB;

class BusesAccommodationController extends BackendController {

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
        }
        $this->data['locations'] = Location::getAll();
        $this->data['buses'] = PilgrimsBus::select('id', 'bus_number as number')->get();
        $this->data['pilgrims'] = BusAccommodation::gePilgrimsWithAccommodation(['filter' => $request->all()]);
        return $this->_view('buses_accommodation/index', 'backend');
    }

    public function create() {
        $this->data['locations'] = Location::getAll();
        return $this->_view('buses_accommodation/create', 'backend');
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
            $pilgrims = $this->getPilgrimsWithNoBusesAccommodation([
                'location' => $request->input('location')
            ]);
            $buses = PilgrimsBus::whereRaw('(num_of_seats-remaining_num_of_seats) > 0')
                    ->select('id', 'bus_number as number', DB::RAW('(num_of_seats-remaining_num_of_seats) as available'))
                    ->where('location_id', $request->input('location'))
                    ->get();

            return _json('success', ['step' => $step, 'buses' => $buses, 'pilgrims_count' => $pilgrims->count()]);
        } else if ($step == 2) {
            DB::beginTransaction();
            try {
                if (!$request->input('buses')) {
                    return _json('error', _lang('app.no_buses_selected'));
                }
                $buses = $this->getSelectedBuses($request->input('buses'));
                //dd($buses);
                //dd($loungues);
                $pilgrims = $this->getPilgrimsWithNoBusesAccommodation([
                    'location' => $request->input('location')
                ]);
                //dd($pilgrims);
                if ($pilgrims->count() == 0) {
                    return _json('error', _lang('app.no_pilgrims_for_accommodation'));
                }
                $this->handleAccommodation($pilgrims, $buses);
                DB::commit();
                //dd($request->all());
                return _json('success', ['step' => $step,'created_at' => date('Y-m-d H-i-s'),'message' => _lang('app.accommodation_done_successfully')]);
            } catch (\Exception $ex) {
                DB::rollback();
                $message = _lang('app.error_is_occured');
                return _json('error', $ex->getMessage() . $ex->getLine());
            }
        }
    }

    public function notify(Request $request) {
        //dd($request->all());
        try {
            $Fcm = new Fcm;
      
            $supervisors_token_and = PilgrimsBus::join('supervisors', 'supervisors.id', '=', 'pilgrims_buses.supervisor_id')
                    ->whereIn('pilgrims_buses.id', $request->input('buses'))
                    ->where('supervisors.device_type', 1)
                    ->pluck('supervisors.device_token')
                    ->toArray();
            $supervisors_token_ios = PilgrimsBus::join('supervisors', 'supervisors.id', '=', 'pilgrims_buses.supervisor_id')
                    ->whereIn('pilgrims_buses.id', $request->input('buses'))
                    ->where('supervisors.device_type', 2)
                    ->pluck('supervisors.device_token')
                    ->toArray();

            $pilgrims_token_and = Pilgrim::join('buses_accommodation', 'pilgrims.id', '=', 'buses_accommodation.pilgrim_id')
                    ->where('buses_accommodation.created_at', $request->input('created_at'))
                    ->where('pilgrims.device_type', 1)
                    ->pluck('pilgrims.device_token')
                    ->toArray();
            $pilgrims_token_ios = Pilgrim::join('buses_accommodation', 'pilgrims.id', '=', 'buses_accommodation.pilgrim_id')
                    ->where('buses_accommodation.created_at', $request->input('created_at'))
                    ->where('pilgrims.device_type', 2)
                    ->pluck('pilgrims.device_token')
                    ->toArray();
     
         
            $notification_supervisors = ['title' => 'HAJJ', 'body' => implode("\n", Pilgrim::$buses_accommodation_phrases['supervisors']), 'type' => 3];
            $notification_pilgrims = ['title' => 'HAJJ', 'body' => implode("\n", Pilgrim::$buses_accommodation_phrases['pilgrims']), 'type' => 3];
            if (count($supervisors_token_and) > 0) {
                $Fcm->send($supervisors_token_and, $notification_supervisors, 'and');
            }
            if (count($supervisors_token_ios) > 0) {
                $Fcm->send($supervisors_token_ios, $notification_supervisors, 'ios');
            }
            if (count($pilgrims_token_and) > 0) {

                $Fcm->send($pilgrims_token_and, $notification_pilgrims, 'and');
            }
            if (count($pilgrims_token_ios) > 0) {
                $Fcm->send($pilgrims_token_ios, $notification_pilgrims, 'ios');
            }
            //dd('here');
            return _json('success', _lang('app.notification_sent_successfully'));
        } catch (\Exception $ex) {
            //dd($ex);
            $message = _lang('app.error_is_occured');
            return _json('error', $message);
        }
    }

    public function destroy(Request $request) {

        $id = $request->input('id');
        DB::beginTransaction();
        try {
            $PilgrimsBus = PilgrimsBus::join('buses_accommodation', 'pilgrims_buses.id', '=', 'buses_accommodation.pilgrim_bus_id')
                    ->where('buses_accommodation.id', $id)
                    ->select('pilgrims_buses.id', 'pilgrims_buses.remaining_num_of_seats', 'pilgrims_buses.busy_seats', 'buses_accommodation.seat_number')
                    ->first();
            if ($PilgrimsBus) {
                $busy_seats = explode(',', $PilgrimsBus->busy_seats);
                //dd($busy_seats->toArray());
                if (!empty($busy_seats)) {
                    if (($key = array_search($PilgrimsBus->seat_number, $busy_seats)) !== false) {
                        unset($busy_seats[$key]);
                    }
                }
            }
            $PilgrimsBus->busy_seats = implode(',', $busy_seats);
            $PilgrimsBus->remaining_num_of_seats = $PilgrimsBus->remaining_num_of_seats - 1;
            $PilgrimsBus->save();
            BusAccommodation::where('id', $id)->delete();
            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            dd($ex);
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'));
        }
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
                    $busy_seats = $buses[$key]['busy_seats'] ? explode(',', $buses[$key]['busy_seats']) : array();
                    $available_seats = $this->getAvailableSeats($busy_seats, $buses[$key]['num_of_seats']);

                    $seats_index = 0;
                    //dd($available_seats);
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
                            if (isset($available_seats[$seats_index])) {
                                $buses[$key]['seat_number'] = $available_seats[$seats_index];
                                $busy_seats[] = $available_seats[$seats_index];
                            }

                            $count_pilgrims_for_accommodation++;
                            fputcsv($file, array('pilgrim_bus_id' => $key, 'pilgrim_id' => $id, 'seat_number' => $buses[$key]['seat_number']), ";");
                            unset($arr[$reservation_no][$id]);
                            //}
                            $seats_index++;
                        }
                        //dd($rooms);
                    }
                    //dd($busy_seats);
                    $busy_seats = implode(',', $busy_seats);
                    $buses[$key]['busy_seats'] = "'$busy_seats'";
                }

                $buses_update = [];
                if ($count_pilgrims_for_accommodation > 0) {
                    $this->_import_csv_pilgrims_buses($path);
                    foreach ($buses as $bus_id => $bus_info) {
                        $buses_update['remaining_num_of_seats'][] = [
                            'id' => $bus_id,
                            'value' => $bus_info['pilgrims'] + $bus_info['remaining_num_of_seats']
                        ];
                        $buses_update['busy_seats'][] = [
                            'id' => $bus_id,
                            'value' => $bus_info['busy_seats']
                        ];
                    }
                    $this->updateValues('App\Models\PilgrimsBus', $buses_update);
                }
            }
            fclose($file);
            unlink($path);
        }
    }

    private function handleAccommodation2($pilgrims, $buses) {

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

    private function getPilgrimsWithNoBusesAccommodation($where_array = array()) {
        $pilgrims = Pilgrim::leftJoin('buses_accommodation', 'pilgrims.id', '=', 'buses_accommodation.pilgrim_id')
                ->where('pilgrims.location_id', $where_array['location'])
                ->whereNull('buses_accommodation.id')
                ->select('pilgrims.id', 'pilgrims.reservation_no')
                ->get();
        return $pilgrims;
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
                ->select('pilgrims_buses.id', 'pilgrims_buses.num_of_seats', 'pilgrims_buses.remaining_num_of_seats', 'pilgrims_buses.busy_seats', DB::RAW('CASE WHEN buses_accommodation.seat_number IS NULL THEN 0 ELSE buses_accommodation.seat_number END as seat_number'), DB::RAW('(pilgrims_buses.num_of_seats-pilgrims_buses.remaining_num_of_seats) as available'), DB::RAW('0 as pilgrims'))
                ->get()
                ->keyBy('id')
                ->toArray();
        return $buses;
    }

    private function _import_csv_pilgrims_buses($csv, $terminated = ";") {

        $query = sprintf("LOAD DATA local INFILE '%s' INTO TABLE buses_accommodation CHARACTER SET utf8 FIELDS TERMINATED BY '$terminated' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\\n' IGNORE 1 LINES (`pilgrim_bus_id`, `pilgrim_id`,`seat_number` ) SET seat_number=TRIM(BOTH '\\r' FROM seat_number),updated_at=current_timestamp(),created_at=current_timestamp()", addslashes($csv));
        return DB::connection()->getpdo()->exec($query);
    }

    private function getAvailableSeats($busy_seats, $avaialable_of_accommodation) {
        $available_seats = [];
        for ($x = 1; $x <= $avaialable_of_accommodation; $x++) {
            if (!in_array($x, $busy_seats)) {

                $available_seats[] = $x;
            }
        }
        return $available_seats;
    }

}
