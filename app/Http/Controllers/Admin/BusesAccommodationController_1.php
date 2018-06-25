<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BackendController;
use App\Models\Location;
use App\Models\Pilgrim;
use App\Models\PilgrimsBus;
use App\Models\Lounge;
use DB;

class BusesAccommodationsController extends BackendController {

    private $step_one_rules = array(
//        'location' => 'required',
//        'pilgrim_class' => 'required',
//        'gender' => 'required',
    );
    private $step_two_rules = array(
    );
    private $step_three_rules = array(
    );

    public function index() {
        $this->data['locations'] = Location::getAll();
        $this->data['buses'] = $this->getBuses();
        //dd($this->data['buses']);
        return $this->_view('buses_accommodation/index', 'backend');
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

            return _json('success', ['step' => $step, 'pilgrims_count' => $pilgrims->count()]);
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
                return _json('success', ['step' => $step, 'message' => _lang('app.accommodation_done_successfully')]);
            } catch (\Exception $ex) {
                DB::rollback();
                $message = _lang('app.error_is_occured');
                return _json('error', $ex->getMessage() . $ex->getLine());
            }
        }
    }

    public function getBuses() {
        $buses = PilgrimsBus::whereRaw('(num_of_seats-remaining_num_of_seats) > 0')
                ->select('id', 'bus_number as number', DB::RAW('(num_of_seats-remaining_num_of_seats) as available'))
                ->get();
        return $buses;
    }

    private function handleAccommodation($pilgrims, $buses) {

        //dd($pilgrims);
        $filename = time() . mt_rand(1, 1000000) . '.csv';
        $path = base_path($filename);
        $file = fopen($path, 'w');
        fputcsv($file, array('pilgrim_bus_id', 'pilgrim_id','seat_number'));
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
                            fputcsv($file, array('pilgrim_bus_id' => $key, 'pilgrim_id' => $id,'seat_number'=>$buses[$key]['seat_number']), ";");
                            unset($arr[$reservation_no][$id]);
                            //}
                        }
                        //dd($rooms);
                    }
                }
                //dd($buses);
                $buses_update=[];
                if ($count_pilgrims_for_accommodation > 0) {
                    $this->_import_csv_pilgrims_buses($path);
                    foreach ($buses as $bus_id => $bus_info) {
                        $buses_update['remaining_num_of_seats'][] = [
                            'id'=>$bus_id,
                            'value' => $bus_info['pilgrims']+$bus_info['remaining_num_of_seats']
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
                ->select('pilgrims_buses.id','pilgrims_buses.remaining_num_of_seats', DB::RAW('CASE WHEN buses_accommodation.seat_number IS NULL THEN 0 ELSE buses_accommodation.seat_number END as seat_number'),DB::RAW('(pilgrims_buses.num_of_seats-pilgrims_buses.remaining_num_of_seats) as available'), DB::RAW('0 as pilgrims'))
                ->get()
                ->keyBy('id')
                ->toArray();
        return $buses;
    }

    private function _import_csv_pilgrims_buses($csv, $terminated = ";") {

        $query = sprintf("LOAD DATA local INFILE '%s' INTO TABLE buses_accommodation CHARACTER SET utf8 FIELDS TERMINATED BY '$terminated' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\\n' IGNORE 1 LINES (`pilgrim_bus_id`, `pilgrim_id`,`seat_number` ) SET seat_number=TRIM(BOTH '\\r' FROM seat_number)", addslashes($csv));
        return DB::connection()->getpdo()->exec($query);
    }

}
