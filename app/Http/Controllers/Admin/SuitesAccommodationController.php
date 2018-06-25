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
use App\Models\SuiteAccommodation;
use App\Models\Lounge;
use App\Helpers\Fcm;
use DB;

class SuitesAccommodationController extends BackendController {

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
        $this->data['types'] = PilgrimAccommodation::$types;
        $this->data['suites'] = Suite::select('id', 'number')->get();
        $this->data['pilgrims'] = SuiteAccommodation::gePilgrimsWithAccommodation(['filter' => $request->all()]);
        return $this->_view('suites_accommodation/index', 'backend');
    }

    public function create(Request $request) {
        $this->data['locations'] = Location::getAll();
        $this->data['pilgrims_class'] = PilgrimClass::getAll();
        $this->data['types'] = SuiteAccommodation::$types;
        return $this->_view('suites_accommodation/create', 'backend');
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
            $suites = Suite::where('gender', $request->input('gender'))->select('id', 'number')->get();
            return _json('success', ['step' => $step, 'suites' => $suites, 'pilgrims_count' => $pilgrims->count()]);
        } else if ($step == 2) {
            DB::beginTransaction();
            try {
                if (!$request->input('lounges')) {
                    return _json('error', _lang('app.no_lounges_selected'));
                }
                $loungues = $this->getSelectedLounges($request->input('lounges'));
                //dd($loungues);
                $pilgrims = $this->getPilgrimsWithNoAccommodation([
                    'location' => $request->input('location'),
                    'pilgrim_class' => $request->input('pilgrim_class'),
                    'gender' => $request->input('gender')]);
                //dd($pilgrims);
                if ($pilgrims->count() == 0) {
                    return _json('error', _lang('app.no_pilgrims_for_accommodation'));
                }
                $this->handleAccommodation($pilgrims, $loungues, $request->input('type'));
                DB::commit();
                //dd($request->all());
                return _json('success', ['step' => $step, 'created_at' => date('Y-m-d H-i-s'), 'message' => _lang('app.accommodation_done_successfully')]);
            } catch (\Exception $ex) {
                DB::rollback();
                dd($ex);
                $message = _lang('app.error_is_occured');
                return _json('error', $ex->getMessage());
            }
        }
    }

    public function notify(Request $request) {
        //dd($request->all());
        try {
            $Fcm = new Fcm;
            $token_and = Pilgrim::join('suites_accommodation', 'pilgrims.id', '=', 'suites_accommodation.pilgrim_id')
                    ->where('suites_accommodation.created_at', $request->input('created_at'))
                    ->where('pilgrims.device_type', 1)
                    ->pluck('pilgrims.device_token')
                    ->toArray();
            //dd($token_and);
            $token_ios = Pilgrim::join('suites_accommodation', 'pilgrims.id', '=', 'suites_accommodation.pilgrim_id')
                    ->where('suites_accommodation.created_at', $request->input('created_at'))
                    ->where('pilgrims.device_type', 2)
                    ->pluck('pilgrims.device_token')
                    ->toArray();
  
            $notification = ['title' => 'HAJJ', 'body' => implode("\n", Pilgrim::$accommodation_phrases), 'type' => 2];
            //$this->create_noti($request->input('request_id'), $notifier_id, $request->input('status'), $notifible_type);
            if (count($token_and) > 0) {
                $send = $Fcm->send($token_and, $notification, 'and');
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
            $lounge = Lounge::join('suites_accommodation', 'lounges.id', '=', 'suites_accommodation.lounge_id')
                    ->where('suites_accommodation.id', $id)
                    ->select('lounges.id', 'lounges.remaining_available_of_accommodation', 'lounges.busy_seats', 'suites_accommodation.number')
                    ->first();
            if ($lounge) {
                $busy_seats = explode(',', $lounge->busy_seats);
                //dd($busy_seats->toArray());
                if (!empty($busy_seats)) {
                    if (($key = array_search($lounge->number, $busy_seats)) !== false) {
                        unset($busy_seats[$key]);
                    }
                }
            }
            $lounge->busy_seats = implode(',', $busy_seats);
            $lounge->remaining_available_of_accommodation = $lounge->remaining_available_of_accommodation - 1;
            $lounge->save();
            SuiteAccommodation::where('id', $id)->delete();
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

    private function handleAccommodation2($pilgrims, $loungues, $type) {

        //dd($pilgrims);
        $filename = time() . mt_rand(1, 1000000) . '.csv';
        $path = base_path($filename);
        $file = fopen($path, 'w');
        fputcsv($file, array('lounge_id', 'pilgrim_id', 'number', 'type'));
        $arr = array();
        $count_pilgrims_for_accommodation = 0;
        if ($pilgrims->count() > 0) {
            foreach ($pilgrims as $one) {
                $arr[$one->reservation_no][$one->id] = $one->id;
            }
            //dd($arr);
            if (count($loungues) > 0) {
                foreach ($loungues as $key => $loungue) {
                    foreach ($arr as $reservation_no => $one) {
                        if (($loungues[$key]['available'] - $loungues[$key]['pilgrims']) == 0) {
                            break;
                        }
                        foreach ($one as $id) {
                            //dd($id);
                            //if (count($id) > 0) {
                            if (($loungues[$key]['available'] - $loungues[$key]['pilgrims']) == 0) {
                                break;
                            }
                            $loungues[$key]['pilgrims'] += 1;
                            $loungues[$key]['number'] += 1;
                            $count_pilgrims_for_accommodation++;
                            fputcsv($file, array('lounge_id' => $key, 'pilgrim_id' => $id, 'number' => $loungues[$key]['number'], 'type' => $type), ";");
                            unset($arr[$reservation_no][$id]);
                            //}
                        }
                        //dd($rooms);
                    }
                }
                //dd($loungues);
                $lounges_update = [];
                if ($count_pilgrims_for_accommodation > 0) {
                    $this->_import_csv_pilgrims_lounges($path);

                    foreach ($loungues as $lounge_id => $lounge_info) {
                        $lounges_update['remaining_available_of_accommodation'][] = [
                            'id' => $lounge_id,
                            'value' => $lounge_info['pilgrims'] + $lounge_info['remaining_available_of_accommodation']
                        ];
                    }
                    $this->updateValues('App\Models\Lounge', $lounges_update);
                }
            }
            fclose($file);
            unlink($path);
        }
    }

    private function handleAccommodation($pilgrims, $loungues, $type) {

        //dd($pilgrims);
        $filename = time() . mt_rand(1, 1000000) . '.csv';
        $path = base_path($filename);
        $file = fopen($path, 'w');
        fputcsv($file, array('lounge_id', 'pilgrim_id', 'number', 'type'));
        $arr = array();
        $count_pilgrims_for_accommodation = 0;
        if ($pilgrims->count() > 0) {
            foreach ($pilgrims as $one) {
                $arr[$one->reservation_no][$one->id] = $one->id;
            }
            //dd($arr);
            if (count($loungues) > 0) {
                foreach ($loungues as $key => $loungue) {
                    $busy_seats = $loungues[$key]['busy_seats'] ? explode(',', $loungues[$key]['busy_seats']) : array();
                    //dd($busy_seats);

                    $available_seats = $this->getAvailableSeats($busy_seats, $loungues[$key]['available_of_accommodation']);
                    $seats_index = 0;
                    foreach ($arr as $reservation_no => $one) {
                        if (($loungues[$key]['available'] - $loungues[$key]['pilgrims']) == 0) {
                            break;
                        }
                        foreach ($one as $id) {
                            //dd($id);
                            //if (count($id) > 0) {
                            if (($loungues[$key]['available'] - $loungues[$key]['pilgrims']) == 0) {
                                break;
                            }
                            $loungues[$key]['pilgrims'] += 1;
                            if (isset($available_seats[$seats_index])) {
                                $loungues[$key]['number'] = $available_seats[$seats_index];
                                $busy_seats[] = $available_seats[$seats_index];
                            }

                            $count_pilgrims_for_accommodation++;
                            fputcsv($file, array('lounge_id' => $key, 'pilgrim_id' => $id, 'number' => $loungues[$key]['number'], 'type' => $type), ";");
                            unset($arr[$reservation_no][$id]);
                            //}
                            $seats_index++;
                        }
                    }
                    $busy_seats = implode(',', $busy_seats);
                    $loungues[$key]['busy_seats'] = "'$busy_seats'";
                }
                //dd($loungues);
                $lounges_update = [];
                if ($count_pilgrims_for_accommodation > 0) {
                    $this->_import_csv_pilgrims_lounges($path);

                    foreach ($loungues as $lounge_id => $lounge_info) {
                        $lounges_update['remaining_available_of_accommodation'][] = [
                            'id' => $lounge_id,
                            'value' => $lounge_info['pilgrims'] + $lounge_info['remaining_available_of_accommodation']
                        ];
                        $lounges_update['busy_seats'][] = [
                            'id' => $lounge_id,
                            'value' => $lounge_info['busy_seats']
                        ];
                    }
                    $this->updateValues('App\Models\Lounge', $lounges_update);
                }
            }
            fclose($file);
            unlink($path);
        }
    }

    private function getPilgrimsWithNoAccommodation($where_array = array()) {
        $pilgrims = Pilgrim::leftJoin('suites_accommodation', 'pilgrims.id', '=', 'suites_accommodation.pilgrim_id')
                ->leftJoin('tents_accommodation', 'pilgrims.id', '=', 'tents_accommodation.pilgrim_id')
                ->leftJoin('buildings_accommodation', 'pilgrims.id', '=', 'buildings_accommodation.pilgrim_id')
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
                ->select('lounges.id', 'lounges.available_of_accommodation', 'lounges.busy_seats', 'lounges.remaining_available_of_accommodation', DB::RAW('(lounges.available_of_accommodation-lounges.remaining_available_of_accommodation) as available'), DB::RAW('CASE WHEN suites_accommodation.number IS NULL THEN 0 ELSE suites_accommodation.number END as number'), DB::RAW('0 as pilgrims'))
                ->orderBy('suites_accommodation.number', 'asc')
                ->groupBy('lounges.id')
                ->get()
                ->keyBy('id')
                ->toArray();
        return $lounges;
    }

    private function getSelectedLounges2($lounges) {
        //dd($lounges);
        $lounges = Lounge::whereIn('id', $lounges)
                ->select('id', DB::RAW('(available_of_accommodation-remaining_available_of_accommodation) as available'), DB::RAW('0 as pilgrims'), DB::RAW('0 as number'))
                ->get()
                ->keyBy('id')
                ->toArray();
        return $lounges;
    }

    private function _import_csv_pilgrims_lounges($csv, $terminated = ";") {

        $query = sprintf("LOAD DATA local INFILE '%s' INTO TABLE suites_accommodation CHARACTER SET utf8 FIELDS TERMINATED BY '$terminated' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\\n' IGNORE 1 LINES (`lounge_id`, `pilgrim_id`,`number`,`type` ) SET type=TRIM(BOTH '\\r' FROM type),updated_at=current_timestamp(),created_at=current_timestamp()", addslashes($csv));
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
