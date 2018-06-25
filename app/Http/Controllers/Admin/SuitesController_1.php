<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Suite;
use App\Models\Lounge;
use Validator;
use DB;

class SuitessController extends BackendController {

    private $rules = array(
        'suite_number' => 'required|unique:suites,number',
        'this_order' => 'required',
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:suites,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:suites,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:suites,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:suites,delete', ['only' => ['delete']]);
    }

    public function index() {

        return $this->_view('suites/index', 'backend');
    }

    public function create() {
        return $this->_view('suites/create', 'backend');
    }

    public function store(Request $request) {
        $this->lounges_rules($request);
        $validator = Validator::make($request->all(), $this->rules, $this->rules_messages());
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $Suite = new Suite;
            $Suite->number = $request->input('suite_number');
            $Suite->this_order = $request->input('this_order');
            $Suite->save();

            $loungue_data = array();
            $loungue_number = $request->input('lounge_number');
            $available_of_accommodation = $request->input('available_of_accommodation');
            $gender = $request->input('gender');
            foreach ($loungue_number as $key => $value) {
                $loungue_data[$value] = array(
                    'number' => $value,
                    'available_of_accommodation' => $available_of_accommodation[$key],
                    'gender' => $gender[$key],
                    'suite_id' => $Suite->id,
                );
            }
            Lounge::insert($loungue_data);

            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', $ex->getMessage(), 400);
        }
    }

    public function edit($id) {
        $find = Suite::find($id);
        if (!$find) {
            return $this->err404();
        }

        $this->data['suite'] = $find;

        return $this->_view('suites/edit', 'backend');
    }

    public function update(Request $request, $id) {
        //dd($request->all());
        $Suite = Suite::find($id);

        if (!$Suite) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->lounges_rules($request);
        $this->rules['suite_number'] = 'required|unique:suites,number,' . $id;
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $Suite->number = $request->input('suite_number');
            $Suite->this_order = $request->input('this_order');
            $Suite->save();

            $loungue_data = array();
            $loungue_number = $request->input('lounge_number');
            $available_of_accommodation = $request->input('available_of_accommodation');
            $gender = $request->input('gender');
            foreach ($loungue_number as $key => $value) {
                $where_arr = array(
                     'number' => $value,
                    'suite_id' => $Suite->id,
               
                );
                //dd($where_arr);
                $data_arr = array(
                    'number' => $value,
                    'suite_id' => $Suite->id,
                    'available_of_accommodation' => $available_of_accommodation[$key],
                    'gender' => $gender[$key],
                );
                //dd($data_arr);
                Lounge::updateOrCreate($where_arr, $data_arr);
            }
            //Lounge::insert($loungue_data);

            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            dd($ex);
            return _json('error', $ex->getMessage() . $ex->getLine(), 400);
        }
    }

    public function destroy_lounge($id) {
        $Lounge = Lounge::find($id);
        if (!$Lounge) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $Lounge->delete();
            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }
    public function destroy($id) {
        $rateQuestion = RateQuestion::find($id);
        if (!$rateQuestion) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $rateQuestionAnswer = RateQuestionAnswer::where('suites_id', $rateQuestion->id)->get();
            foreach ($rateQuestionAnswer as $value) {
                RateQuestionAnswerTranslation::where('suites_answer_id', $value->id)->delete();
            }
            RateQuestionAnswer::where('suites_id', $rateQuestion->id)->delete();

            RateQuestionTranslation::where('suites_id', $rateQuestion->id)->delete();
            $rateQuestion->delete();
            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {
        $question = Suite::select([
                    'id', 'number', "this_order"
        ]);

        return \Datatables::eloquent($question)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('suites', 'edit') || \Permissions::check('suites', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('suites', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('suites.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('suites', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Suites.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->rawColumns(['options'])
                        ->make(true);
    }

    private function lounges_rules($request) {
        $loungue_number = $request->input('lounge_number');
        $available_of_accommodation = $request->input('available_of_accommodation');
        $gender = $request->input('gender');
        if ($loungue_number && count($loungue_number) > 0) {
            foreach ($loungue_number as $key => $one) {
                $this->rules['lounge_number.' . $key] = 'required';
            }
        }
        if ($available_of_accommodation && count($available_of_accommodation) > 0) {
            foreach ($available_of_accommodation as $key => $one) {
                $this->rules['available_of_accommodation.' . $key] = 'required';
            }
        }
        if ($gender && count($gender) > 0) {
            foreach ($gender as $key => $one) {
                $this->rules['gender.' . $key] = 'required';
            }
        }
    }

    private function rules_messages() {
        return [
            'required' => _lang('app.this_field_is_required'),
            'unique' => _lang('app.this_field_is_taken'),
        ];
    }

}
