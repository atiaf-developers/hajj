<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Suite;
use App\Models\Lounge;
use Validator;
use DB;

class LoungesController extends BackendController {

    private $rules = array(
        'number' => 'required|unique:lounges,number',
        'available_of_accommodation' => 'required',
        'this_order' => 'required',
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:lounges,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:lounges,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:lounges,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:lounges,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        $Suite = Suite::find($request->input('suite'));
        if (!$Suite) {
            return $this->err404();
        }
        $this->data['suite'] = $Suite;
        return $this->_view('lounges/index', 'backend');
    }

    public function create() {
        return $this->_view('lounges/create', 'backend');
    }

    public function store(Request $request) {
        $suite_id = $request->input('suite_id');
        $this->rules['number'] = "required|unique:lounges,number,NULL,id,suite_id,$suite_id";
        $validator = Validator::make($request->all(), $this->rules, $this->rules_messages());
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        try {
            $Lounge = new Lounge;
            $Lounge->number = $request->input('number');
            $Lounge->available_of_accommodation = $request->input('available_of_accommodation');
            $Lounge->this_order = $request->input('this_order');
            $Lounge->suite_id = $request->input('suite_id');
            $Lounge->save();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            return _json('error', $ex->getMessage(), 400);
        }
    }

    public function show($id) {
        $find = Lounge::find($id);

        if ($find) {
            return _json('success', $find);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }

    public function update(Request $request, $id) {
        $Lounge = Lounge::find($id);
        if (!$Lounge) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $suite_id = $request->input('suite_id');
        $this->rules['number'] = "required|unique:lounges,number,$id,id,suite_id,$suite_id";
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        try {
            $Lounge->number = $request->input('number');
            $Lounge->available_of_accommodation = $request->input('available_of_accommodation');
            $Lounge->this_order = $request->input('this_order');
            $Lounge->save();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            return _json('error', $ex->getMessage() . $ex->getLine(), 400);
        }
    }

    public function destroy($id) {
        $Lounge = Lounge::find($id);
        if (!$Lounge) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        try {
            $Lounge->delete();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {
        //dd( $request->input('suite'));
        $question = Lounge::where('suite_id', $request->input('suite'))
                ->select([
            'id', 'number', "available_of_accommodation",DB::RAW('(available_of_accommodation-remaining_available_of_accommodation) as remaining'), "this_order"
        ]);

        return \Datatables::eloquent($question)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('lounges', 'edit') || \Permissions::check('lounges', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('lounges', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a onclick = "Lounges.edit(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('lounges', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Lounges.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
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
                        ->filterColumn('gender', function($query, $keyword) {
                            if ($keyword == 'male') {
                                $query->where("gender", 1);
                            } else {
                                $query->where("gender", 2);
                            }
                        })
                        ->rawColumns(['options', 'gender'])
                        ->make(true);
    }

    private function rules_messages() {
        return [
            'required' => _lang('app.this_field_is_required'),
            'unique' => _lang('app.this_field_is_taken'),
        ];
    }

}
