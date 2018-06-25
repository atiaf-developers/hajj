<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Tent;
use Validator;
use DB;

class TentsController extends BackendController {

    private $rules = array(
        'number' => 'required|unique:tents,number',
        'gender' => 'required',
        'type' => 'required',
        'available_of_accommodation' => 'required',
        'this_order' => 'required',
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:tents,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:tents,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:tents,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:tents,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('tents/index', 'backend');
    }

    public function store(Request $request) {

        $validator = Validator::make($request->all(), $this->rules, $this->rules_messages());
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        try {
            $tent = new Tent;
            $tent->number = $request->input('number');
            $tent->gender = $request->input('gender');
            $tent->type = $request->input('type');
            $tent->available_of_accommodation = $request->input('available_of_accommodation');
            $tent->this_order = $request->input('this_order');
            $tent->save();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            return _json('error', $ex->getMessage(), 400);
        }
    }

    public function show($id) {
        $find = Tent::find($id);

        if ($find) {
            return _json('success', $find);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }

    public function update(Request $request, $id) {
        $tent = Tent::find($id);
        if (!$tent) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules['number'] = 'required|unique:tents,number,' . $id;
        $validator = Validator::make($request->all(), $this->rules, $this->rules_messages());

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        try {
            $tent->number = $request->input('number');
            $tent->gender = $request->input('gender');
            $tent->type = $request->input('type');
            $tent->available_of_accommodation = $request->input('available_of_accommodation');
            $tent->this_order = $request->input('this_order');
            $tent->save();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            return _json('error', $ex->getMessage() . $ex->getLine(), 400);
        }
    }

    public function destroy($id) {
        $tent = Tent::find($id);
        if (!$tent) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        try {
            $tent->delete();
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
        $tents = Tent::select([
            'id', 'number','gender','type',"available_of_accommodation",DB::RAW('(available_of_accommodation-remaining_available_of_accommodation) as remaining'), "this_order"
        ]);

        return \Datatables::eloquent($tents)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('tents', 'edit') || \Permissions::check('tents', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('tents', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a onclick = "Tents.edit(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('tents', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Tents.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }


                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->editColumn('gender', function ($item) {
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
                        ->editColumn('type', function ($item) {
                            if ($item->type == 1) {
                                $message = _lang('app.tent');
                                $class = 'label-success';
                            } else {
                                $message = _lang('app.lounge');
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
                        ->filterColumn('type', function($query, $keyword) {
                            if ($keyword == 'tent') {
                                $query->where("type", 1);
                            } else {
                                $query->where("type", 2);
                            }
                        })
                        ->escapeColumns([])
                        ->make(true);
    }

    private function rules_messages() {
        return [
            'required' => _lang('app.this_field_is_required'),
            'unique' => _lang('app.this_field_is_taken'),
        ];
    }

}
