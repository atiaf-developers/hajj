<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Suite;
use App\Models\Lounge;
use Validator;
use DB;

class SuitesController extends BackendController {

    private $rules = array(
        'number' => 'required|unique:suites,number',
        'gender' => 'required',
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

        $validator = Validator::make($request->all(), $this->rules, $this->rules_messages());
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        try {
            $Suite = new Suite;
            $Suite->number = $request->input('number');
            $Suite->gender = $request->input('gender');
            $Suite->this_order = $request->input('this_order');
            $Suite->save();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            return _json('error', $ex->getMessage(), 400);
        }
    }
      public function show($id) {
        $find = Suite::find($id);

        if ($find) {
            return _json('success', $find);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
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
        $Suite = Suite::find($id);
        if (!$Suite) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules['number'] = 'required|unique:suites,number,' . $id;
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        try {
            $Suite->number = $request->input('number');
            $Suite->gender = $request->input('gender');
            $Suite->this_order = $request->input('this_order');
            $Suite->save();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            return _json('error', $ex->getMessage() . $ex->getLine(), 400);
        }
    }

    public function destroy($id) {
        $Suite = Suite::find($id);
        if (!$Suite) {
           return _json('error', _lang('app.error_is_occured'), 404);
        }

        try {
            $Suite->delete();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            if ($ex->getCode() == 23000) {
                return _json('error',  _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {
        $question = Suite::select([
                    'id', 'number', "this_order","gender"
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
                                    $back .= '<a onclick = "Suites.edit(this);return false;" data-id = "' . $item->id . '">';
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
                                if (\Permissions::check('lounges', 'open')) {
                                    $back .= '<li>';
                                     $back .= '<a href="' . url('admin/lounges?suite='. $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.loungues');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                          ->addColumn('gender', function ($item) {
                            if ($item->gender == 1) {
                                $message = _lang('app.male');
                                $class = 'label-success';
                            } else {
                                $message = _lang('app.female');
                                $class = 'label-danger';
                            }
                            $back = '<span class="label label-sm ' . $class . '">' . $message . '</span>';
                            return $back;
                        })
                        ->rawColumns(['options', 'gender'])
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
