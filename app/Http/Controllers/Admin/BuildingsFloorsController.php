<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Building;
use App\Models\BuildingFloor;
use Validator;
use DB;

class BuildingsFloorsController extends BackendController {

    private $rules = array(
        'number' => 'required',
        'gender' => 'required',
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:buildings_floors,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:buildings_floors,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:buildings_floors,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:buildings_floors,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        $Building = Building::find($request->input('building'));
        if (!$Building) {
            return $this->err404();
        }
        $this->data['building'] = $Building;
        return $this->_view('buildings_floors/index', 'backend');
    }

    public function create() {
        return $this->_view('buildings_floors/create', 'backend');
    }

    public function store(Request $request) {
        $building_id = $request->input('building_id');
        $this->rules['number'] = "required|unique:buildings_floors,number,NULL,id,building_id,$building_id";
        $validator = Validator::make($request->all(), $this->rules, $this->rules_messages());
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        try {
            $BuildingFloor = new BuildingFloor;
            $BuildingFloor->number = $request->input('number');
            $BuildingFloor->gender = $request->input('gender');
            $BuildingFloor->building_id = $request->input('building_id');
            $BuildingFloor->save();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            return _json('error', $ex->getMessage(), 400);
        }
    }

    public function show($id) {
        $find = BuildingFloor::find($id);

        if ($find) {
            return _json('success', $find);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }

    public function update(Request $request, $id) {
        $BuildingFloor = BuildingFloor::find($id);
        if (!$BuildingFloor) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
         $building_id = $request->input('building_id');
        $this->rules['number'] = "required|unique:buildings_floors,number,$id,id,building_id,$building_id";
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        try {
            $BuildingFloor->number = $request->input('number');
            $BuildingFloor->gender = $request->input('gender');
            $BuildingFloor->save();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            return _json('error', $ex->getMessage() . $ex->getLine(), 400);
        }
    }

    public function destroy($id) {
        $BuildingFloor = BuildingFloor::find($id);
        if (!$BuildingFloor) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        try {
            $BuildingFloor->delete();
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
        //dd( $request->input('building'));
        $question = BuildingFloor::where('building_id', $request->input('building'))
                ->select([
            'id', 'number', "gender"
        ]);

        return \Datatables::eloquent($question)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('buildings_floors', 'edit') || \Permissions::check('buildings_floors', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('buildings_floors', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a onclick = "BuildingsFloors.edit(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('buildings_floors', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "BuildingsFloors.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('buildings_floors_rooms', 'open')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . url('admin/buildings_floors_rooms?floor=' . $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.rooms');
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
