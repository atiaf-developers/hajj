<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BackendController;
use App\Models\Location;
use App\Models\Pilgrim;
use App\Models\PilgrimAccommodation;
use App\Models\PilgrimClass;
use App\Models\Tent;
use App\Models\TentAccommodation;
use App\Models\Lounge;
use App\Helpers\Fcm;
use DB;

class TentsAccommodationController extends BackendController {

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
        $this->data['pilgrims'] = TentAccommodation::gePilgrimsWithAccommodation(['filter' => $request->all()]);
        return $this->_view('tents_accommodation/index', 'backend');
    }

    public function create(Request $request) {
        $this->data['locations'] = Location::getAll();
        $this->data['pilgrims_class'] = PilgrimClass::getAll();
        return $this->_view('tents_accommodation/create', 'backend');
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
            $tents = Tent::whereRaw('(available_of_accommodation-remaining_available_of_accommodation) > 0')
                    ->where('gender', $request->input('gender'))
                    ->select('id', 'number', DB::RAW('(available_of_accommodation-remaining_available_of_accommodation) as available'))
                    ->get();
            return _json('success', ['step' => $step, 'tents' => $tents, 'pilgrims_count' => $pilgrims->count()]);
        } else if ($step == 2) {
            DB::beginTransaction();
            try {
                if (!$request->input('tents')) {
                    return _json('error', _lang('app.no_tents_selected'));
                }
                $tents = $this->getSelectedTents($request->input('tents'));
                //dd($tents);
                $pilgrims = $this->getPilgrimsWithNoAccommodation([
                    'location' => $request->input('location'),
                    'pilgrim_class' => $request->input('pilgrim_class'),
                    'gender' => $request->input('gender')]);
                //dd($pilgrims);
                if ($pilgrims->count() == 0) {
                    return _json('error', _lang('app.no_pilgrims_for_accommodation'));
                }
                $this->handleAccommodation($pilgrims, $tents);
                DB::commit();
                //dd($request->all());
                return _json('success', ['step' => $step,'created_at' => date('Y-m-d H-i-s'), 'message' => _lang('app.accommodation_done_successfully')]);
            } catch (\Exception $ex) {
                DB::rollback();
                $message = _lang('app.error_is_occured');
                return _json('error', $ex->getMessage());
            }
        }
    }

    public function getTents() {
        $tents = Tent::whereRaw('(available_of_accommodation-remaining_available_of_accommodation) > 0')
                ->select('id', 'number', DB::RAW('(available_of_accommodation-remaining_available_of_accommodation) as available'))
                ->get();
        return $tents;
    }

    public function notify(Request $request) {
        //dd($request->all());
        try {
            $Fcm = new Fcm;
               $token_and = Pilgrim::join('tents_accommodation', 'pilgrims.id', '=', 'tents_accommodation.pilgrim_id')
                    ->where('tents_accommodation.created_at', $request->input('created_at'))
                    ->where('pilgrims.device_type', 1)
                    ->pluck('pilgrims.device_token')
                    ->toArray();
               $token_ios = Pilgrim::join('tents_accommodation', 'pilgrims.id', '=', 'tents_accommodation.pilgrim_id')
                    ->where('tents_accommodation.created_at', $request->input('created_at'))
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
            $Tent = Tent::join('tents_accommodation', 'tents.id', '=', 'tents_accommodation.tent_id')
                    ->where('tents_accommodation.id', $id)
                    ->select('tents.id', 'tents.remaining_available_of_accommodation')
                    ->first();
            if ($Tent) {

                $Tent->remaining_available_of_accommodation = $Tent->remaining_available_of_accommodation - 1;
                $Tent->save();
                TentAccommodation::where('id', $id)->delete();
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
        $tents = Lounge::where('suite_id', $suite_id)
                ->whereRaw('(available_of_accommodation-remaining_available_of_accommodation) > 0')
                ->select('id', 'number', DB::RAW('(available_of_accommodation-remaining_available_of_accommodation) as available'))
                ->get()
                ->toArray();
        return _json('success', $tents);
    }

    private function handleAccommodation($pilgrims, $tents) {

        //dd($pilgrims);
        $filename = time() . mt_rand(1, 1000000) . '.csv';
        $path = base_path($filename);
        $file = fopen($path, 'w');
        fputcsv($file, array('tent_id', 'pilgrim_id'));
        $arr = array();
        $count_pilgrims_for_accommodation = 0;
        if ($pilgrims->count() > 0) {
            foreach ($pilgrims as $one) {
                $arr[$one->reservation_no][$one->id] = $one->id;
            }
            //dd($arr);
            if (count($tents) > 0) {
                foreach ($tents as $key => $loungue) {
                    foreach ($arr as $reservation_no => $one) {
                        if (($tents[$key]['available'] - $tents[$key]['pilgrims']) == 0) {
                            break;
                        }
                        foreach ($one as $id) {
                            //dd($id);
                            //if (count($id) > 0) {
                            if (($tents[$key]['available'] - $tents[$key]['pilgrims']) == 0) {
                                break;
                            }
                            $tents[$key]['pilgrims'] += 1;
                            $count_pilgrims_for_accommodation++;
                            fputcsv($file, array('tent_id' => $key, 'pilgrim_id' => $id), ";");
                            unset($arr[$reservation_no][$id]);
                            //}
                        }
                        //dd($rooms);
                    }
                }
                //dd($tents);
                $tents_update = [];
                if ($count_pilgrims_for_accommodation > 0) {
                    $this->_import_csv_pilgrims_lounges($path);

                    foreach ($tents as $tent_id => $tent_info) {
                        $tents_update['remaining_available_of_accommodation'][] = [
                            'id' => $tent_id,
                            'value' => $tent_info['pilgrims'] + $tent_info['remaining_available_of_accommodation']
                        ];
                    }
                    $this->updateValues('App\Models\Tent', $tents_update);
                }
            }
            fclose($file);
            unlink($path);
        }
    }

    private function getPilgrimsWithNoAccommodation($where_array = array()) {
        $pilgrims = Pilgrim::leftJoin('tents_accommodation', 'pilgrims.id', '=', 'tents_accommodation.pilgrim_id')
                ->leftJoin('suites_accommodation', 'pilgrims.id', '=', 'suites_accommodation.pilgrim_id')
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

        $query = sprintf("LOAD DATA local INFILE '%s' INTO TABLE tents_accommodation CHARACTER SET utf8 FIELDS TERMINATED BY '$terminated' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\\n' IGNORE 1 LINES (`tent_id`, `pilgrim_id` ) SET pilgrim_id=TRIM(BOTH '\\r' FROM pilgrim_id),updated_at=current_timestamp(),created_at=current_timestamp()", addslashes($csv));
        return DB::connection()->getpdo()->exec($query);
    }

}
